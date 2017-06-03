<?php

namespace Toplan\PhpSms;

use SuperClosure\Serializer;
use Toplan\TaskBalance\Balancer;
use Toplan\TaskBalance\Task;

/**
 * Class Sms
 *
 * @author toplan<toplan710@gmail.com>
 */
class Sms
{
    const TASK_NAME = 'PhpSms';
    const TYPE_SMS = 1;
    const TYPE_VOICE = 2;

    /**
     * Agent instances.
     *
     * @var array
     */
    protected static $agents = [];

    /**
     * The dispatch scheme of agents.
     *
     * @var array
     */
    protected static $scheme = [];

    /**
     * The configuration information of agents.
     *
     * @var array
     */
    protected static $agentsConfig = [];

    /**
     * Whether to use the queue.
     *
     * @var bool
     */
    protected static $enableQueue = false;

    /**
     * How to use the queue system.
     *
     * @var \Closure
     */
    protected static $howToUseQueue = null;

    /**
     * Available hooks.
     *
     * @var array
     */
    protected static $availableHooks = [
        'beforeRun',
        'beforeDriverRun',
        'afterDriverRun',
        'afterRun',
    ];

    /**
     * Closure serializer.
     *
     * @var Serializer
     */
    protected static $serializer = null;

    /**
     * Data container.
     *
     * @var array
     */
    protected $smsData = [
        'type'         => self::TYPE_SMS,
        'to'           => null,
        'templates'    => [],
        'templateData' => [],
        'content'      => null,
        'voiceCode'    => null,
    ];

    /**
     * The name of first agent.
     *
     * @var string|null
     */
    protected $firstAgent = null;

    /**
     * Whether pushed to the queue system.
     *
     * @var bool
     */
    protected $pushedToQueue = false;

    /**
     * State container.
     *
     * @var array
     */
    protected $state = [];

    /**
     * Constructor
     *
     * @param bool $autoBoot
     */
    public function __construct($autoBoot = true)
    {
        if ($autoBoot) {
            self::bootstrap();
        }
    }

    /**
     * Bootstrap the task.
     */
    public static function bootstrap()
    {
        if (!self::taskInitialized()) {
            self::configuration();
            self::initTask();
        }
    }

    /**
     * Whether the task initialized.
     *
     * Note: 判断drivers是否为空不能用'empty',因为在TaskBalance库的中Task类的drivers属性是受保护的(不可访问),
     * 虽然通过魔术方法可以获取到其值,但在其目前版本(v0.4.2)其内部却并没有使用'__isset'魔术方法对'empty'或'isset'函数进行逻辑补救.
     *
     * @return bool
     */
    protected static function taskInitialized()
    {
        $task = self::getTask();

        return (bool) count($task->drivers);
    }

    /**
     * Get the task instance.
     *
     * @return Task
     */
    public static function getTask()
    {
        if (!Balancer::hasTask(self::TASK_NAME)) {
            Balancer::task(self::TASK_NAME);
        }

        return Balancer::getTask(self::TASK_NAME);
    }

    /**
     * Configuration.
     */
    protected static function configuration()
    {
        $config = [];
        if (!count(self::scheme())) {
            self::initScheme($config);
        }
        $diff = array_diff_key(self::scheme(), self::$agentsConfig);
        self::initAgentsConfig(array_keys($diff), $config);
        self::validateConfig();
    }

    /**
     * Initialize the dispatch scheme.
     *
     * @param array $config
     */
    protected static function initScheme(array &$config)
    {
        $config = empty($config) ? include __DIR__ . '/../config/phpsms.php' : $config;
        $scheme = isset($config['scheme']) ? $config['scheme'] : [];
        self::scheme($scheme);
    }

    /**
     * Initialize the configuration information.
     *
     * @param array $agents
     * @param array $config
     */
    protected static function initAgentsConfig(array $agents, array &$config)
    {
        if (empty($agents)) {
            return;
        }
        $config = empty($config) ? include __DIR__ . '/../config/phpsms.php' : $config;
        $agentsConfig = isset($config['agents']) ? $config['agents'] : [];
        foreach ($agents as $name) {
            $agentConfig = isset($agentsConfig[$name]) ? $agentsConfig[$name] : [];
            self::config($name, $agentConfig);
        }
    }

    /**
     * Validate the configuration.
     *
     * @throws PhpSmsException
     */
    protected static function validateConfig()
    {
        if (!count(self::scheme())) {
            throw new PhpSmsException('Please configure at least one agent.');
        }
    }

    /**
     * Initialize the task.
     */
    protected static function initTask()
    {
        foreach (self::scheme() as $name => $scheme) {
            // parse higher-order scheme
            $settings = [];
            if (is_array($scheme)) {
                $settings = self::parseScheme($scheme);
                $scheme = $settings['scheme'];
            }
            // create driver
            self::getTask()->driver("$name $scheme")->work(function ($driver) use ($settings) {
                $agent = self::getAgent($driver->name, $settings);
                $smsData = $driver->getTaskData();
                extract($smsData);
                $template = isset($templates[$driver->name]) ? $templates[$driver->name] : 0;
                if ($type === self::TYPE_VOICE) {
                    $agent->voiceVerify($to, $voiceCode, $template, $templateData);
                } elseif ($type === self::TYPE_SMS) {
                    $agent->sendSms($to, $content, $template, $templateData);
                }
                $result = $agent->result();
                if ($result['success']) {
                    $driver->success();
                }
                unset($result['success']);

                return $result;
            });
        }
    }

    /**
     * Parse the higher-order dispatch scheme.
     *
     * @param array $options
     *
     * @return array
     */
    protected static function parseScheme(array $options)
    {
        $agentClass = Util::pullFromArrayByKey($options, 'agentClass');
        $sendSms = Util::pullFromArrayByKey($options, 'sendSms');
        $voiceVerify = Util::pullFromArrayByKey($options, 'voiceVerify');
        $backup = Util::pullFromArrayByKey($options, 'backup') ? 'backup' : '';
        $scheme = implode(' ', array_values($options)) . " $backup";

        return compact('agentClass', 'sendSms', 'voiceVerify', 'scheme');
    }

    /**
     * Get the agent instance by name.
     *
     * @param string $name
     * @param array  $options
     *
     * @throws PhpSmsException
     *
     * @return mixed
     */
    public static function getAgent($name, array $options = [])
    {
        if (!self::hasAgent($name)) {
            $scheme = self::scheme($name);
            $config = self::config($name);
            if (is_array($scheme) && empty($options)) {
                $options = self::parseScheme($scheme);
            }
            if (isset($options['scheme'])) {
                unset($options['scheme']);
            }
            $className = "Toplan\\PhpSms\\{$name}Agent";
            if (isset($options['agentClass'])) {
                $className = $options['agentClass'];
                unset($options['agentClass']);
            }
            if (isset($options['sendSms']) || isset($options['voiceVerify'])) {
                $config = array_merge($config, $options);
                self::$agents[$name] = new ParasiticAgent($config);
            } elseif (class_exists($className)) {
                self::$agents[$name] = new $className($config);
            } else {
                throw new PhpSmsException("Do not support `$name` agent.");
            }
        }

        return self::$agents[$name];
    }

    /**
     * Whether has the specified agent.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasAgent($name)
    {
        return isset(self::$agents[$name]);
    }

    /**
     * Set or get the dispatch scheme.
     *
     * @param mixed $name
     * @param mixed $scheme
     *
     * @return mixed
     */
    public static function scheme($name = null, $scheme = null)
    {
        return Util::operateArray(self::$scheme, $name, $scheme, null, function ($key, $value) {
            if (is_string($key)) {
                self::modifyScheme($key, is_array($value) ? $value : "$value");
            } elseif (is_int($key)) {
                self::modifyScheme($value, '');
            }
        });
    }

    /**
     * Modify the dispatch scheme of agent.
     *
     * @param $key
     * @param $value
     *
     * @throws PhpSmsException
     */
    protected static function modifyScheme($key, $value)
    {
        if (self::taskInitialized()) {
            throw new PhpSmsException("Modify the dispatch scheme of `$key` agent failed, because the task system has already started.");
        }
        self::validateAgentName($key);
        self::$scheme[$key] = $value;
    }

    /**
     * Set or get the configuration information.
     *
     * @param mixed $name
     * @param mixed $config
     * @param bool  $override
     *
     * @throws PhpSmsException
     *
     * @return array
     */
    public static function config($name = null, $config = null, $override = false)
    {
        if (is_array($name) && is_bool($config)) {
            $override = $config;
        }

        return Util::operateArray(self::$agentsConfig, $name, $config, [], function ($key, $value) {
            if (is_array($value)) {
                self::modifyConfig($key, $value);
            }
        }, $override, function (array $origin) {
            $nameList = array_keys($origin);
            foreach ($nameList as $name) {
                if (self::hasAgent("$name")) {
                    self::getAgent("$name")->config([], true);
                }
            }
        });
    }

    /**
     * Modify the configuration information.
     *
     * @param string $key
     * @param array  $value
     *
     * @throws PhpSmsException
     */
    protected static function modifyConfig($key, array $value)
    {
        self::validateAgentName($key);
        self::$agentsConfig[$key] = $value;
        if (self::hasAgent($key)) {
            self::getAgent($key)->config($value);
        }
    }

    /**
     * Validate the agent name.
     * Expected type is string, except the string of number.
     *
     * @param string $name
     *
     * @throws PhpSmsException
     */
    protected static function validateAgentName($name)
    {
        if (!$name || !is_string($name) || preg_match('/^[0-9]+$/', $name)) {
            throw new PhpSmsException("Expected the agent name `$name` to be a string, witch except the string of number.");
        }
    }

    /**
     * Tear down agent use scheme and prepare to create and start a new task,
     * so before do it must destroy old task instance.
     */
    public static function cleanScheme()
    {
        Balancer::destroy(self::TASK_NAME);
        self::$scheme = [];
    }

    /**
     * Tear down all the configuration information of agent.
     */
    public static function cleanConfig()
    {
        self::config([], true);
    }

    /**
     * Create a instance for send sms,
     * you can also set templates or content at the same time.
     *
     * @param mixed $agentName
     * @param mixed $tempId
     *
     * @return Sms
     */
    public static function make($agentName = null, $tempId = null)
    {
        $sms = new self();
        $sms->smsData['type'] = self::TYPE_SMS;
        if (is_array($agentName)) {
            $sms->template($agentName);
        } elseif ($agentName && is_string($agentName)) {
            if ($tempId === null) {
                $sms->content($agentName);
            } elseif (is_string($tempId) || is_int($tempId)) {
                $sms->template($agentName, "$tempId");
            }
        }

        return $sms;
    }

    /**
     * Create a instance for send voice verify code,
     * you can also set verify code at the same time.
     *
     * @param int|string|null $code
     *
     * @return Sms
     */
    public static function voice($code = null)
    {
        $sms = new self();
        $sms->smsData['type'] = self::TYPE_VOICE;
        $sms->smsData['voiceCode'] = $code;

        return $sms;
    }

    /**
     * Set whether to use the queue system,
     * and define how to use it.
     *
     * @param mixed $enable
     * @param mixed $handler
     *
     * @return bool
     */
    public static function queue($enable = null, $handler = null)
    {
        if ($enable === null && $handler === null) {
            return self::$enableQueue;
        }
        if (is_callable($enable)) {
            $handler = $enable;
            $enable = true;
        }
        self::$enableQueue = (bool) $enable;
        if (is_callable($handler)) {
            self::$howToUseQueue = $handler;
        }

        return self::$enableQueue;
    }

    /**
     * Set the recipient`s mobile number.
     *
     * @param string $mobile
     *
     * @return $this
     */
    public function to($mobile)
    {
        $this->smsData['to'] = trim((string) $mobile);

        return $this;
    }

    /**
     * Set the sms content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->smsData['content'] = trim((string) $content);

        return $this;
    }

    /**
     * Set the template ids.
     *
     * @param mixed $name
     * @param mixed $tempId
     *
     * @return $this
     */
    public function template($name, $tempId = null)
    {
        Util::operateArray($this->smsData['templates'], $name, $tempId);

        return $this;
    }

    /**
     * Set the template data.
     *
     * @param mixed $name
     * @param mixed $value
     *
     * @return $this
     */
    public function data($name, $value = null)
    {
        Util::operateArray($this->smsData['templateData'], $name, $value);

        return $this;
    }

    /**
     * Set the first agent.
     *
     * @param string $name
     *
     * @return $this
     */
    public function agent($name)
    {
        $this->firstAgent = (string) $name;

        return $this;
    }

    /**
     * Start send.
     *
     * If call with a `true` parameter, this system will immediately start request to send sms whatever whether to use the queue.
     * if the current instance has pushed to the queue, you can recall this method in queue system without any parameter,
     * so this mechanism in order to make you convenient to use this method in queue system.
     *
     * @param bool $immediately
     *
     * @return mixed
     */
    public function send($immediately = false)
    {
        if (!self::$enableQueue || $this->pushedToQueue) {
            $immediately = true;
        }
        if ($immediately) {
            return Balancer::run(self::TASK_NAME, [
                'data'   => $this->getData(),
                'driver' => $this->firstAgent,
            ]);
        }

        return $this->push();
    }

    /**
     * Push to the queue system.
     *
     * @throws \Exception | PhpSmsException
     *
     * @return mixed
     */
    public function push()
    {
        if (!is_callable(self::$howToUseQueue)) {
            throw new PhpSmsException('Please define how to use the queue system by the `queue` method.');
        }
        try {
            $this->pushedToQueue = true;

            return call_user_func_array(self::$howToUseQueue, [$this, $this->getData()]);
        } catch (\Exception $e) {
            $this->pushedToQueue = false;
            throw $e;
        }
    }

    /**
     * Get all of the data.
     *
     * @param null|string $key
     *
     * @return mixed
     */
    public function all($key = null)
    {
        if (is_string($key) && isset($this->smsData["$key"])) {
            return $this->smsData[$key];
        }

        return $this->smsData;
    }

    /**
     * The alias of `all` method.
     *
     * @param null|string $key
     *
     * @return mixed
     */
    public function getData($key = null)
    {
        return $this->all($key);
    }

    /**
     * Define the static hook methods by overload static method.
     *
     * @param string $name
     * @param array  $args
     *
     * @throws PhpSmsException
     */
    public static function __callStatic($name, $args)
    {
        $name = $name === 'beforeSend' ? 'beforeRun' : $name;
        $name = $name === 'afterSend' ? 'afterRun' : $name;
        $name = $name === 'beforeAgentSend' ? 'beforeDriverRun' : $name;
        $name = $name === 'afterAgentSend' ? 'afterDriverRun' : $name;
        if (!in_array($name, self::$availableHooks)) {
            throw new PhpSmsException("Do not find method `$name`.");
        }
        $handler = $args[0];
        $override = isset($args[1]) ? (bool) $args[1] : false;
        if (!is_callable($handler)) {
            throw new PhpSmsException("Please call method `$name` with a callable parameter.");
        }
        $task = self::getTask();
        $task->hook($name, $handler, $override);
    }

    /**
     * Define the hook methods by overload method.
     *
     * @param string $name
     * @param array  $args
     *
     * @throws PhpSmsException
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        try {
            $this->__callStatic($name, $args);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Serialize magic method.
     *
     * @return array
     */
    public function __sleep()
    {
        try {
            $this->state['scheme'] = self::serializeOrDeserializeScheme(self::scheme());
            $this->state['agentsConfig'] = self::config();
            $this->state['handlers'] = self::serializeHandlers();
        } catch (\Exception $e) {
            //swallow exception
        }

        return ['smsData', 'firstAgent', 'pushedToQueue', 'state'];
    }

    /**
     * Deserialize magic method.
     */
    public function __wakeup()
    {
        if (empty($this->state)) {
            return;
        }
        self::$scheme = self::serializeOrDeserializeScheme($this->state['scheme']);
        self::$agentsConfig = $this->state['agentsConfig'];
        Balancer::destroy(self::TASK_NAME);
        self::bootstrap();
        self::reinstallHandlers($this->state['handlers']);
    }

    /**
     * Get a closure serializer.
     *
     * @return Serializer
     */
    protected static function getSerializer()
    {
        if (!self::$serializer) {
            self::$serializer = new Serializer();
        }

        return self::$serializer;
    }

    /**
     * Serialize or deserialize the scheme.
     *
     * @param array $scheme
     *
     * @return array
     */
    protected static function serializeOrDeserializeScheme(array $scheme)
    {
        foreach ($scheme as $name => &$options) {
            if (is_array($options)) {
                self::serializeOrDeserializeClosureAndReplace($options, 'sendSms');
                self::serializeOrDeserializeClosureAndReplace($options, 'voiceVerify');
            }
        }

        return $scheme;
    }

    /**
     * Serialize the hooks` handlers.
     *
     * @return array
     */
    protected static function serializeHandlers()
    {
        $task = self::getTask();
        $hooks = (array) $task->handlers;
        foreach ($hooks as &$handlers) {
            foreach (array_keys($handlers) as $key) {
                self::serializeOrDeserializeClosureAndReplace($handlers, $key);
            }
        }

        return $hooks;
    }

    /**
     * Reinstall hooks` handlers.
     *
     * @param array $handlers
     */
    protected static function reinstallHandlers(array $handlers)
    {
        $serializer = self::getSerializer();
        foreach ($handlers as $hookName => $serializedHandlers) {
            foreach ($serializedHandlers as $index => $handler) {
                if (is_string($handler)) {
                    $handler = $serializer->unserialize($handler);
                }
                self::$hookName($handler, $index === 0);
            }
        }
    }

    /**
     * Serialize or deserialize the specified closure and then replace the original value.
     *
     * @param array      $options
     * @param int|string $key
     */
    protected static function serializeOrDeserializeClosureAndReplace(array &$options, $key)
    {
        if (!isset($options[$key])) {
            return;
        }
        $serializer = self::getSerializer();
        if (is_callable($options[$key])) {
            $options[$key] = (string) $serializer->serialize($options[$key]);
        } elseif (is_string($options[$key])) {
            $options[$key] = $serializer->unserialize($options[$key]);
        }
    }
}
