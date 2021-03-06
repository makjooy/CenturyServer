<?php
//该文件内容不会自动更新

//方法性能分析
define("MEMCACHE_KEY_COMMANDANALYSIS", "centurywar_commandAnalysis_%d");
define("MEMCACHE_KEY_COMMANDANALYSIS_LIST", "centurywar_commandAnalysis_list_%d");

//测试数据库连接
define("MEMCACHE_KEY_TESTCONTENT", "centurywar_testContent_%d_%d");
define("MEMCACHE_KEY_TESTCONTENT_LIST", "centurywar_testContent_list_%d");

//用户基本信息表
define("MEMCACHE_KEY_ACCOUNT", "centurywar_Account_%d");
define("MEMCACHE_KEY_ACCOUNT_LIST", "centurywar_Account_list_%d");

//Chat 聊天数据节点
define('MEMCACHE_KEY_NODE_SERVER_GAMEUID_POSITION', 'centurywar_chat_node_%d_%d_%d');
//Chat 聊天指针
define('MEMCACHE_KEY_SERVER_GAMEUID_POINTER', 'centurywar_chat_pointer_%d_%d');
//Chat 免费余量
define('MEMCACHE_KEY_SURPLUS_SERVER_GAMEUID', 'centurywar_chat_surplus_%d_%d');

//用户建筑表 
define("MEMCACHE_KEY_BUILDING", "centurywar_Building_%d");
define("MEMCACHE_KEY_BUILDING_LIST", "centurywar_Building_list_%d");

//用户建筑表 
define("MEMCACHE_KEY_RUNTIME", "centurywar_Runtime_%d");
define("MEMCACHE_KEY_RUNTIME_LIST", "centurywar_Runtime_list_%d");

//用户MAPPING表 
define("MEMCACHE_KEY_MAPPING", "centurywar_mapping_%d_%d");
define("MEMCACHE_KEY_MAPPING_UID", "centurywar_mapping_uid_%d_%s");
define("MEMCACHE_KEY_MAPPING_LIST", "centurywar_mapping_list_%d_%d");

//XML 缓存
define("MEMCACHE_KEY_XML_FILE", "xml_cache_file_%s");
define("MEMCACHE_KEY_XML_GROUP","xml_cache_group_%s");
define("MEMCACHE_KEY_XML_ITEM", "xml_cache_item_%s_%s");
