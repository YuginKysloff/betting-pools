
var plugin = {

//css плагины

  'normalize.css': true,

  'reset.css': false,

//js плагины
  'jquery-3.0.0': true,

  'jq-cookie-2.1.3': true,

  'Mozart': true, 

  'hamburger': false,

  'fancySelect': true,

  'wayPoints': false,

  'vivus': false,

  'data-q': true

};

//Пути к плагинам
var pluginPath = {

  'normalize.css': {
    js: '',
    css: 'plugin/normalize.css/01normalize.scss'
  },

  'reset.css': {
    js: '',
    css: 'plugin/reset.css/01reset.css'
  },

  'jquery-3.0.0': {
    js: 'plugin/jquery/jquery-3.0.0.min.js',
    css: ''
  },

  'jq-cookie-2.1.3': {
    js: 'plugin/cookie.js/jq-cookie-2.1.3.js',
    css: ''
  },

  'Mozart': {
    js: 'plugin/process-js/process.js',
    css: ''
  },

  'hamburger': {
    js: 'plugin/hamburger/hamburger.js',
    css: 'plugin/hamburger/hamburger.scss'
  },

  'fancySelect': {
    js: 'plugin/fancySelect/fancySelect.js',
    css: 'plugin/fancySelect/fancySelect.css'
  },

  'wayPoints': {
    js: 'plugin/wayPoints/wayPoints.min.js',
    css: ''
  },

  'vivus': {
    js: 'plugin/vivus/vivus.js',
    css: 'plugin/vivus/qq.css'
  },

  'data-q': {
    js: 'plugin/data-q/data-q.js',
    css: ''
  }

}

var userScriptPath = 'js/**/*.js';

var enabledPlugins = [];

for (var key in plugin) {
  if (plugin[key]) {
    enabledPlugins.push(key);
  }  
}

var getEnabledPluginsCss = enabledPlugins.map(function(plug) {

  if (pluginPath[plug]['css'] !== '') {
    var temp = [];
    temp.push(pluginPath[plug]['css']);
    return temp;
  } 
}).reduce(function(a, b) {
  return a.concat(b);
}, []).filter(function( element ) {
   return element !== undefined;
});


//Записываем в массив пути к плагинам
var getEnabledPluginsJs = enabledPlugins.map(function(plug) {

  if (pluginPath[plug]['js'] !== '') {
    var temp = [];
    temp.push(pluginPath[plug]['js']);
    return temp;
  } 
}).reduce(function(a, b) {
  return a.concat(b);
}, []).filter(function( element ) {
   return element !== undefined;
});

//Записываем в массив путь к пользовательским скриптам
getEnabledPluginsJs.push(userScriptPath);

var enablePlugin = enabledPlugins.map(function(plug) {
  var temp = [];
  temp.push(pluginPath[plug]['js']);   
  return temp;
}).reduce(function(a, b) {
  return a.concat(b);
}, []);

//Считаем общие количество плагинов
function numberPlugins() {
  var counter = 0;
  for (var key in plugin) {
    if (plugin != undefined) {
        counter++
    }  
  }
  return counter;
}


//Выводим в консоль список подключенных плагинов
console.log(' ');
console.log(' '+enablePlugin.length+' из '+numberPlugins()+' плагина поключено');
console.log(' ');
for (var i = 0;  i <= enabledPlugins.length-1; i++) {
  
  console.log('   ==> '+enabledPlugins[i]);
}
console.log(' ');

export {getEnabledPluginsJs, getEnabledPluginsCss};