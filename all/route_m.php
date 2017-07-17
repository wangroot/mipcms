<?php
namespace app\route;
use think\Route;
use think\DB;

Route::get('/m',$mipInfo['httpType'] .$mipInfo["mipDomain"]);

Route::domain($mipInfo['mipDomain'], function() use($mipInfo) {
    Route::rule('/','m/Index/index');
    Route::rule('/m','/');
    Route::rule('/sitemap.xml','m/Index/sitemap');
    Route::rule(['/xml/:id' => ['m/Index/xml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);

    if ($mipInfo['superSites']) {
        $articlesCategory = db('ArticlesCategory')->where('pid',0)->select();
        foreach ($articlesCategory as $k => $v) {
            $url_name = $v['url_name'];
            Route::domain('m.'.$url_name, function() use($url_name,$mipInfo) {

                Route::rule('/','m/Index/index?category='.$url_name);

                Route::rule(['[:sub]/index_<page>' => ['m/Article/index?category='.$url_name.'&sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);
                Route::rule([$mipInfo['articleModelUrl'].'/<id>_<page>'=>['m/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
                Route::rule([$mipInfo['articleModelUrl'].'/:id'=>['m/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
                Route::rule(['[:sub]' => ['m/Article/index?category='.$url_name.'&sub=:sub',[],[]]]);
            });
        }
    } else {
        if ($mipInfo['aritcleLevelRemove']) {
            Route::rule(['[:category]/index_<page>' => ['m/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/[:sub]/index_<page>' => ['m/Article/index?sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['index_<page>' => ['m/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['/index' => ['m/Article/index',['ext'=>'html'],['id'=>'\d+']]]);
            Route::rule(['/publish' => ['m/Article/publish',['ext'=>'html'],[]]]);
            Route::rule([$mipInfo['articleModelUrl'].'/<id>_<page>'=>['m/Article/articleDetail',['ext'=>'html'],['id'=>'\w+'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule([$mipInfo['articleModelUrl'].'/:id'=>['m/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]/[:sub]' => ['m/Article/index?sub=:sub',[],[]]]);
            Route::rule(['[:category]' => ['m/Article/index',[],[]]]);
            Route::rule(['[:category]/[:keys]' => ['m/Article/index',['ext'=>'html'],['keys'=>'index']]]);
            Route::rule($mipInfo['articleModelUrl'],'m/Article/index');

        } else {
            Route::group($mipInfo['articleModelUrl'], [
                '[:category]/index_<page>'  =>'m/Article/index',
                'index_<page>'=>'m/Article/index',
                '/index'  =>['m/Article/index',['ext'=>'html'],['id'=>'\d+']],
                '/publish'  =>['m/Article/publish',['ext'=>'html'],[]],
                '<id>_<page>'=>  ['m/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']],
                ':id'=>  ['m/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']],
                '[:category]'  => ['m/Article/index',['[a-zA-Z]+(?!-)$'],[]],
                ],[],['category'=>'[a-zA-Z]+','page'=>'\d+','ext'=>'html']);
        }
    }
},['ext'=>'html'],['id'=>'\d+','name'=>'\w+']);
