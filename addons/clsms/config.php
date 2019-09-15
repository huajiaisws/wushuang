<?php

return array (
  0 => 
  array (
    'name' => 'key',
    'title' => '验证码',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => trim(config('site.sms_account')),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),

  1 => 
  array (
    'name' => 'secret',
    'title' => '密码',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => trim(config('site.sms_password')),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),

  2 => 
  array (
    'name' => 'key1',
    'title' => '会员营销短信',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'M0617261',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => 'secret1',
    'title' => '会员营销密钥',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'LTRt9Wrlfb9def',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),

  4 => 
  array (
    'name' => 'sign',
    'title' => '短信签名',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => trim(config('site.sms_qianming')),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);

