<?php

// 获取文章
$api->get('/article/show', 'Article\ArticleController@show');
$api->get('/article/one', 'Article\ArticleController@one');
$api->post('/article/delete', 'Article\ArticleController@delete');
// 获取文章
$api->post('/article/save', 'Article\ArticleController@saveArticle');