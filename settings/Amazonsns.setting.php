<?php
return array(
  'amazon_sns_api_key' => array(
    'group_name' => 'Amazon SNS Preferences',
    'group' => 'com.ginkgostreet.amazonsns',
    'name' => 'amazon_sns_api_key',
    'type' => 'String',
    'default' => '',
    'add' => '4.4',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Amazon SNS API Key',
    'help_text' => 'The API Key to use for connecting to Amazon SNS',
  ),
  'amazon_sns_api_secret' => array(
    'group_name' => 'Amazon SNS Preferences',
    'group' => 'com.ginkgostreet.amazonsns',
    'name' => 'amazon_sns_api_secret',
    'type' => 'String',
    'default' => '',
    'add' => '4.4',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Amazon SNS API Secret',
    'help_text' => 'The API Secret to use for connecting to Amazon SNS',
  ),
  'amazon_sns_region' => array(
    'group_name' => 'Amazon SNS Preferences',
    'group' => 'com.ginkgostreet.amazonsns',
    'name' => 'amazon_sns_region',
    'type' => 'String',
    'default' => 'us-east-1',
    'add' => '4.4',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Amazon SNS Region',
    'help_text' => 'The Region to use for connecting to Amazon SNS',
  )
);