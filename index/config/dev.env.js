var merge = require('webpack-merge');
var prodEnv = require('./prod.env');

module.exports = merge(prodEnv, {
  NODE_ENV: '"development"',
  // API:'"http://fagzlz.hqshop.com:"',
  API:'"http://gzlzfa.com:"',
  PORT:'"8082"', // 8082 8081  8088 8092
  // API:'"http://fagzlz.a6shop.net:"',
  // PORT:'"80"',
  proxy:'"local"'
});
