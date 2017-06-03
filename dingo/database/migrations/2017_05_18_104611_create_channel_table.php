<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unique()->comment('会员表的主键ID');
            $table->string('jigou')->default('')->comment("组织机构代码");
            $table->string('name')->default('')->comment("商铺名称");
            $table->string('region')->default('')->comment("商铺地址主键");
            $table->string('region_name')->default('')->comment("商铺地址名称");
            $table->string('address')->default('')->comment("商铺详细地址");
            $table->string('image_url')->default('')->comment("商铺营业执照");
            $table->string('status')->default(0)->comment("0:为审核,1:审核成功,2:审核失败");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('channel_infos');
    }
}
