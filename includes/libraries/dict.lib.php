<?php

if (!defined('IN_ECM'))
{
    die('Hacking attempt');
}
class Dict extends Object{
	
	private $items;
	private $_mod_dict;
	private $field = "id,name,code,parentid,isdefault,isshow,statusid,en";
	//private $allow = "3,24,164,165,168,166,167,614,225,226,227,228,229,35,38,36,37,41,47,42,43,44,45,39,46,176,177,178,179,189,197,190,191,192,193,198,194,199,195,481,196,482,1119,50,51,52,53,54,57,56,58,61,55,212,213,214,215,218,219,220,222,223,224,1290,128,130,131,132,133,134,135,136,137,138,139,140,141,129,142,143,145,146,147,148,149,150,151,152,153,154,155,156,157,158,144,925,298,417,422,866,867,868,869,870,871,872,873,874,430,450,483,484,835,836,838,839,840,842,843,844,845,846,847,848,849,850,851,852,853,854,855,856,857,858,860,861,862,864,33484,859,31170,31171,31172,31173,31174,31169,837,841,863,518,528,529,530,531,532,331,1232,1233,31233,31234,31276,31310,31405,31406,332,371,375,33999,34000,34001,34002,34003,34004,34005,34006,34007,34008,34009,33981,33982,33983,33984,33985,33986,33987,33988,33989,33990,33991,33992,33993,33994,33995,33996,33997,33998,34010,34011,34012,34013,34014,34015,34016,34017,34018,34019,34020,34021,34022,34023,34024,34025,34026,34027,34028,34057,34058,34059,34060,34071,34072,34073,34074,34075,34076,34077,34078,34079,34080,34081,34082,34083,34084,34085,34061,34062,34063,34064,34065,34066,34067,34068,34069,34070,376,603,611,1343,299,307,1120,33525,33526,33527,33528,33547,345,350,357,770,771,773,774,775,777,784,787,786,790,792,797,31193,31194,31195,31196,31197,31198,761,763,766,768,772,1344,33691,33692,33693,33694,33695,33696,33697,33699,33700,33701,33702,33703,33704,33705,33706,33707,33708,33709,33710,33711,33698,33712,33713,33714,33715,435,1230,1199,436,437,431,432,433,434,313,31655,31657,31658,31659,31660,31661,31662,31663,31665,31666,31667,31668,31669,31670,31671,31672,657,658,659,660,661,662,663,664,665,666,667,31570,31571,31572,31573,31574,31575,31576,31577,31578,31579,31580,31581,31582,31583,31584,31585,31586,31587,31588,31589,31590,31591,31592,31593,31594,31595,31596,31597,31653,31654,31693,31700,31689,31692,31645,31646,31647,31648,31649,31650,31651,31652,31673,31674,31675,31676,31677,31603,31604,31605,31606,31607,31608,31678,31679,31610,31611,31612,31613,31614,31609,31615,31616,31680,31690,31617,31694,31695,31696,31697,31698,31699,31635,31636,31691,31637,31638,31639,31640,31681,31682,31683,31598,31618,31644,31641,31642,31643,31684,31685,31687,31688,644,316,317,318,319,645,321,315,629,621,622,623,626,627,630,633,634,635,636,655,651,320,637,638,639,640,641,642,643,628,653,654,669,670,671,672,656,673,1221,31622,31623,31624,31625,31631,31632,31633,675,1652,1651,31620,676,677,31626,31627,31628,31629,31630,678,680,31621,686,687,689,31701,31702,31703,31704,31705,31706,31709,31710,31711,31634,31712,33659,363,33716,33717,33718,33719,33720,33721,33722,33723,33724,33725,33726,33727,33728,33729,33730,33731,33732,33733,33734,33735,33736,33737,33738,33739,33740,808,810,811,812,814,821,823,824,827,829,833,834,807,370,31175,31176,31177,31178,31179,31180,809,805,803,800,798,1345,2000,2157,2224,40715,40716,40891,40127,40128,40129,40130,2158,2159,2449,40913,40914,40915,40916,40931,2192,2194,2235,2236,2239,2241,2242,2243,2247,2254,2195,2196,2255,2257,2258,2198,2259,2260,2261,2262,2263,2265,2636,2289,2706,40250,40251,40252,40253,40255,40256,41073,41074,41072,41075,41076,41077,41078,41079,41080,41081,41082,41083,41111,41112,41113,41114,41115,41116,41117,41118,41119,41120,41121,41122,41123,41124,41125,41126,41127,41128,41129,41130,41131,41132,41133,41134,41135,41136,41137,41138,41139,41140,41141,41142,41143,41144,41145,41146,41147,41148,41085,41086,41087,41088,41089,41090,41091,41092,41093,41094,41095,41096,41097,41098,41099,41100,41101,41102,41084,41103,41104,41105,41106,41107,41108,41109,41110,2234,2256,2264,2613,2180,2181,2182,2183,2184,2185,2367,2368,2370,2371,2372,2374,2376,2377,2378,2379,2381,2382,2383,2384,2385,2386,2387,2388,2389,2390,2391,2392,2393,2394,2395,2396,2397,2398,2399,2400,2401,2402,2403,2404,2614,2684,40283,40284,40285,40286,40287,40288,40990,40991,40992,40993,40994,40995,40996,40997,40998,40999,41000,41001,41002,41003,41004,41005,41006,41007,41008,41009,41010,41011,41012,41013,41014,41015,2366,2369,2373,2375,2380,2186,2187,2188,2189,2190,2191,2411,2412,2414,2415,2416,2418,2420,2421,2422,2423,2425,2426,2427,2428,2429,2430,2431,2432,41016,41017,41018,41019,41020,41021,41022,41023,41024,41025,41026,41027,41028,41029,41030,41031,41032,41033,41034,41035,41036,41037,41038,41039,41040,41041,2433,2434,2435,2436,2437,2438,2439,2440,2441,2442,2443,2444,2445,2446,2447,2448,2615,2685,40271,40272,40273,40274,40275,40276,2410,2413,2417,2419,2424,2318,2213,2214,2215,2216,2217,2454,2455,2457,2458,2459,2461,2462,2463,2464,2465,2466,2467,2468,2469,2470,2471,2472,2473,2474,2475,2476,2477,2479,2480,2481,2483,2485,2486,2487,2488,2489,2490,2491,2492,2493,2478,40265,40266,40267,40268,40269,40270,40907,2456,2460,2482,2523,2587,2588,2589,2528,2586,2021,2067,2068,2079,2085,2069,2070,2524,2525,2526,2078,2081,2082,2083,2345,2074,2075,2076,2089,2090,2091,2092,2093,2094,2095,2096,2097,2098,2099,2527,2112,2113,2121,2122,2125,2130,2689,2131,2132,2124,2123,2133,2134,2520,2135,2136,2137,2521,2522,2141,2142,2146,2147,2148,2149,2150,2151,2152,2153,2143,2144,2145,2031,2032,2035,2038,2039,2033,2034,2036,2037,2046,2047,2048,2049,2050,2051,2052,2053,2054,2055,2056,2057,2058,3000,3184,3215,3210,51717,51718,51719,51720,51721,51722,51723,51724,51725,51726,51727,51728,51729,51730,51731,51732,51733,51734,51735,51736,51737,51738,51739,51740,51741,51742,51743,51744,51745,51746,51747,51748,3214,3222,3302,3316,3707,3235,3430,51665,51666,51667,51668,51669,51670,51671,51672,51673,51674,51675,51676,51677,51678,51679,51680,51681,51682,51683,51684,51685,51686,51687,51688,3585,3587,3590,3592,3706,3431,3432,3433,3584,3594,3595,3598,3599,3604,3610,3611,3613,3614,3616,50199,50200,50201,50202,50203,50204,3434,3246,3631,3632,3633,3634,3635,3636,3637,3660,3669,51459,3639,3640,3641,3643,3644,3645,3646,3647,3648,3649,3650,3651,3652,3653,3654,3655,3656,3657,50193,50194,50195,50196,50197,50198,3667,3668,3670,3671,3672,3673,3674,3675,3658,3659,3661,3662,3663,3665,3638,3642,3664,3248,3622,3623,3624,3625,3626,3259,3260,3261,3263,3264,3185,3199,3200,3282,3283,3536,3537,3538,3540,3541,3202,3203,51643,51644,51642,51645,51646,51647,51648,51649,51650,51651,51652,51653,51654,51655,51656,51657,51658,51659,51660,51661,51662,51663,51664,3475,3477,3485,3486,3488,3489,3490,3492,3476,3478,3481,3483,3618,3507,3509,50211,50212,50213,50214,50215,50216,3495,3501,3502,3504,3505,3016,3049,3054,3055,3056,3057,3058,3059,3060,3355,3061,3062,51575,51577,51578,51579,51580,51581,51582,51583,51584,51574,51573,51576,3074,3075,3077,3080,3076,3079,3093,3095,3096,3097,3098,3099,3100,3101,3102,3103,3104,3105,3107,3108,3109,3110,3113,3119,3124,3118,3123,3116,3126,3130,3134,3135,3136,3138,3139,3145,3149,3150,3168,3169,3162,3141,3146,3147,3148,3155,3156,3159,3164,3165,3166,3170,3171,3172,3161,3173,3174,3175,3177,3178,3026,3027,3029,3028,3034,3039,3040,3041,3042,3043,3045,4000,4016,4022,4023,4026,4028,4030,4032,4034,4035,4037,4038,4039,4036,4264,4041,4638,4042,4043,4044,4290,4291,4049,4052,4056,4057,4060,4063,4050,4051,4061,4062,4053,4065,4066,4067,4068,4069,4070,4072,4073,4071,4075,4992,4994,4017,4018,4019,4020,70521,70522,4112,70809,70810,70811,4115,4118,4183,4192,4648,4093,70639,70640,70641,70642,70643,70644,70645,70646,70647,70648,70649,70650,70651,70652,70653,70654,70655,70656,70657,70658,70659,70660,70661,70662,70663,70664,70665,70666,70667,70668,70669,70670,70671,70672,70673,70756,70737,70738,70739,70740,70741,70742,70743,70744,70745,70746,70747,70748,70749,70750,70751,70752,70753,70754,70755,70757,70758,70759,70760,70761,70762,70763,70764,70765,70766,70767,70768,70769,70770,70771,70772,70773,70774,70775,70776,70777,70778,70779,70780,70781,70782,70783,70784,70785,70786,70787,70788,70789,70790,70791,70792,70793,70794,70795,70796,70797,70798,70799,70800,70801,70802,70803,70680,70681,70688,70689,70690,70691,70682,70692,70693,70694,70695,70696,70697,70698,70699,70700,70701,70702,70703,70704,70705,70706,70707,70708,70709,70710,70711,70712,70713,70714,70715,70716,70717,70718,70719,70720,70721,70722,70723,70724,70725,70726,70727,70728,70729,70730,70731,70732,70733,70734,70735,70736,70683,70684,70685,70686,70687,4551,4552,4553,4554,4615,4616,4617,4618,4621,4622,4623,4624,4625,4626,4627,4633,4647,70624,70625,70627,70628,70629,70630,70631,70632,70633,70634,70635,70636,70637,70638,70674,70675,70676,70677,70678,70679,4620,4076,4081,70497,70498,70499,70523,4555,70496,4102,70583,70584,70585,70586,70587,70588,70589,70590,70591,70592,70593,70594,70595,70596,70597,70598,4355,4357,4360,4362,4366,4649,70021,70022,70023,70574,70575,70576,70577,70578,70579,70580,70581,70582,4364,4365,4371,4376,4378,4381,4384,4386,4391,4700,4706,4707,4708,4712,4716,70018,70019,70020,4147,4150,70493,4505,70000,70001,70002,70003,70004,70005,4477,4478,4479,4480,4481,4482,4484,4485,4486,4488,4489,4490,4491,4492,4493,4494,4495,4496,4497,4498,4499,4500,4501,4502,4503,4504,4506,4507,4508,4510,4512,4513,4514,4515,4516,4517,4518,4519,4520,4483,4487,4509,4155,4158,4159,4160,4161,4795,4107,4754,4755,4762,4763,4766,4775,70006,70007,70008,70009,70010,70011,70600,70601,70602,70603,70604,70605,70599,70606,70607,70608,70609,70610,70611,70612,70613,70614,70615,70616,70617,70618,70619,70620,70621,70622,70623,4405,4411,4416,4418,4421,4404,4426,4430,4635,4395,4397,4634,4402,4406,4650,4753,6000,6276,6277,6972,6971,6970,6969,6968,6967,6725,6726,6732,6395,6404,6547,6405,6406,6407,6408,6397,6403,6433,6434,6435,6439,6440,6441,6442,6443,6535,6536,6537,6538,6539,6540,6541,6542,6543,6544,6545,6546,6548,6549,6550,6552,61188,6554,6555,6556,6557,6558,6559,6560,6561,6562,60000,60001,60002,60003,60004,60005,6432,6436,6551,6413,6414,6415,6416,6417,61098,6409,6410,6411,6412,6349,61302,61303,61304,61305,61306,61307,61308,61309,61310,61311,61312,6569,6322,6679,6683,6687,60024,60025,60026,60027,60028,60029,61244,61245,61246,61247,61248,61249,61250,61251,61252,61253,61254,61255,61256,61257,61258,61259,61260,61261,61262,61263,61264,61265,61266,61267,6671,6677,6678,6346,6335,6336,6338,6340,6341,6342,6343,6345,6438,61268,61269,61270,61271,61272,61273,61274,61275,61276,61277,61278,61279,61280,61281,61282,61283,61284,61285,61286,61287,61288,61289,61290,61291,61292,6354,6356,6840,6841,6842,6850,6853,6862,60006,60007,60008,60009,60010,60011,6357,6447,6451,6348,6351,6352,6353,6007,6106,6107,6108,6109,6110,60334,6606,6120,6124,6126,6123,61171,6128,6122,6154,6167,6168,6174,6429,6430,6190,6191,6192,6193,6013,6016,6017,6019,6020,6024,6027,6028,6029,6030,6031,6032,35501,35504,35503,35507,35508,35509,35511,35506,35505,35512,35516,110011,35515";
	
	function __construct($items){
	    $this->items     = $items;
	    $this->_mod_dict = &m("dict");
	}
	
	function menuDesign(){
	    $items = current($this->items);
	    $res = $this->_mod_dict->find(array(
	       'conditions' => "parentid = '{$items["design"]}'",
	       'order'      => "sequenceno",
	       'fields'      => $this->field
	    ));
	    $menu = $res;
	    $this->getRealMenu($res, $menu);
	    $tree = $this->getTree($menu,$items["design"]);
	    $menu = array();
	    foreach($tree as $key => $val){
	        $this->filter($val, $menu);
	    }

	    return $menu;
	}
	
	function menuDeep(){
	    $items = current($this->items);
	    $res = $this->_mod_dict->find(array(
	       'conditions' => "parentid='{$items["deep"]}'",
	       'order'      => "sequenceno",
	       'fields'      => $this->field
	    ));
	    $menu = $res;
	    $this->getRealMenu($res, $menu);
	    $tree = $this->getTree($menu,$items["deep"]);
	    $menu = array();
	    foreach($tree as $key => $val){
	        $this->filter($val, $menu);
	    }

	    return $menu;
	}
	
	
	function getMenu(){
	    if(empty($this->items)){
	        $this->_error("非法操作", $this);
	        return false;
	    }
	    $menu = array();
	    
	    $designMenu = $this->menuDesign();
	    
	   // $this->filter($designMenu);
	    
	    $deepMenu   = $this->menuDeep();
	    
        //$this->filter($deepMenu);
	    
	    $menu["design"] = $designMenu;
	    $menu["deep"]   = $deepMenu;
	    return $menu;
	}
	
	
	function getRealMenu($res, &$menus){
	    $ids = array();
	    foreach((array)$res as $key => $val){
	            $ids[] = $val["id"];
	    }

	    $res = $this->_mod_dict->find(array(
	        'conditions' => "parentid ".db_create_in($ids),
	        'order'      => "sequenceno",
	        'fields'      => $this->field
	    ));
	    
	    
	    if(empty($res)) return;
	    
	    foreach($res as $key => $val){
	          $menus[$val["id"]] = $val;
	    }
	    
	    return $this->getRealMenu($res, $menus);
	}
	
	function getTree($data, $pId=0){
	    $tree = array();
	    foreach($data as $k => $v)
	    {
	        if($v['parentid'] == $pId)
	        {
	            $v['children'] = $this->getTree($data, $v['id']);
	            $tree[] = $v;
	            unset($data[$k]);
	        }
	    }
	    return $tree;
	}
	
	function filter($arr, &$menu){
	    foreach($arr['children'] as $key =>$val){
	        if(!empty($val['children'])){
	            $this->filter($val, $menu);
	        }else{
	            unset($arr["children"]);
	            $menu[$arr["id"]] = $arr;
	            //return $menu;
	        }
	    }
	}
}
?>