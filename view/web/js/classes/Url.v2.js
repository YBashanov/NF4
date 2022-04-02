/**
 * getParams() - получить объект параметров из адресной строки (после знака ?)
 *
 * getParam(key) - получить значение только 1 параметра из адресной строки
 *
 * getFilters() - получить все значения из строки filters_id
 *
 * getFilter(key) - получить одно значение из строки filters_id
 *
 * getHashes() - получить все значения после знака # (в блоке hash)
 *
 * getHash(key) - получить одно значение после знака # (в блоке hash)
 *
 * getHashFilters() - получить все значения из строки filters_id (в блоке hash)
 *
 * getHashFilter(key) - получить одно значение из строки filters_id (в блоке hash)
 *
 * setParams(urlParams, flags, callback) - Установить параметры в урл.
 *
 *    - flags={append:true} дозапись к тем параметрам урла, которые уже есть.
 *
 *    - flags={pageReload:true} перезагрузка страницы после изменения параметров урл.
 *
 * setParam(key, value, flags, callback) - Установить один из GET-параметров (после знака ?) в режиме дозаписи
 */
var Url = {
    /**
     * получить объект параметров из адресной строки (после знака ?)
     */
    getParams : function() {
        if (location) {
            var params = {};
            var search = (location.search).substring(1);

            var array1 = search.split("&");
            var array2;
            for (var i=0; i<array1.length; i++) {
                array2 = array1[i].split("=");
                params[array2[0]] = array2[1];
            }

            return params;
        }
        else return null;
    },


    /**
     * получить значение только 1 параметра из адресной строки
     */
    getParam : function(key){
        if (location) {
            var search = (location.search).substring(1);

            var array1 = search.split("&");
            var array2;
            for (var i=0; i<array1.length; i++) {
                array2 = array1[i].split("=");
                if (array2[0] == key) {
                    return array2[1];
                }
            }

            return null;
        }
        else return null;
    },


    /**
     * получить все значения из строки filters_id
     * возвращает {}
     */
    getFilters : function(){
        var filters_id = Url.getParam("filters_id");
        var filterObject = {};

        if (filters_id) {
            filters_id = decodeURI(filters_id);
            var filters = filters_id.split(',');
            var filter, f_a;
            var filterLength = filters.length;
            for (var f = 0; f < filterLength; f++) {
                filter = filters[f];

                f_a = filter.split('_');
                var fKey = f_a[0];
                var fValue = f_a[1];

                filterObject[fKey] = fValue;
            }
        }

        return filterObject;
    },


    /**
     * получить одно значение из строки filters_id
     */
    getFilter : function(key){
        var filters_id = Url.getParam("filters_id");
        if (filters_id) {
            var filters = filters_id.split(',');
            var filter, f_a;
            var filterLength = filters.length;
            for (var f = 0; f < filterLength; f++) {
                filter = filters[f];

                f_a = filter.split('_');

                var fKey = f_a[0];
                var fValue = f_a[1];

                if (key == fKey) {
                    return fValue;
                }
            }
        }
        return null;
    },


    /**
     * получить все значения после знака # (в блоке hash)
     * возвращает {}
     */
    getHashes : function() {
        if (location) {
            var params = {};
            var hash = (location.hash).substring(1);
            var decodeHash = decodeURI(hash);

            var array1 = decodeHash.split("&");
            var array2;
            for (var i=0; i<array1.length; i++) {
                array2 = array1[i].split("=");
                params[array2[0]] = array2[1];
            }
            return params;
        }
        else return null;
    },


    /**
     * получить одно значение после знака # (в блоке hash)
     */
    getHash : function(key){
        if (location) {
            var hash = (location.hash).substring(1);
            var decodeHash = decodeURI(hash);

            var array1 = decodeHash.split("&");
            var array2;
            for (var i=0; i<array1.length; i++) {
                array2 = array1[i].split("=");
                if (array2[0] == key) {
                    return array2[1];
                }
            }
            return null;
        }
        else return null;
    },


    /**
     * получить все значения из строки filters_id (в блоке hash)
     * возвращает {}
     */
    getHashFilters : function(){
        var filters_id = Url.getHash("filters_id");
        var filterObject = {};

        if (filters_id) {
            filters_id = decodeURI(filters_id);
            var filters = filters_id.split(',');
            var filter, f_a;
            var filterLength = filters.length;
            for (var f = 0; f < filterLength; f++) {
                filter = filters[f];

                f_a = filter.split('_');
                var fKey = f_a[0];
                var fValue = f_a[1];

                filterObject[fKey] = fValue;
            }
        }

        return filterObject;
    },


    /**
     * получить одно значение из строки filters_id (в блоке hash)
     */
    getHashFilter : function(key){
        var filters_id = Url.getHash("filters_id");
        if (filters_id) {
            var filters = filters_id.split(',');
            var filter, f_a;
            var filterLength = filters.length;
            for (var f = 0; f < filterLength; f++) {
                filter = filters[f];

                f_a = filter.split('_');

                var fKey = f_a[0];
                var fValue = f_a[1];

                if (key == fKey) {
                    return fValue;
                }
            }
        }
        return null;
    },


    /**
     * Установить параметры в урл
     * flags={}  параметры добавления
     * - append:true - дозапись к тем параметрам урла, которые уже есть
     * - pageReload:true - перезагрузка страницы после изменения параметров урл
     */
    setParams : function(urlParams, flags, callback) {
        if (flags == undefined) flags = {};
        if (flags.append == undefined) flags.append = true;
        if (flags.pageReload == undefined) flags.pageReload = true;


        if (flags.append) {
            var params = Url.getParams();
            if (params) {
                for (var key in params) {
                    if (urlParams[key] == undefined) {
                        urlParams[key] = params[key];
                    }
                }
            }
        }

        var urlString = "";
        if (urlParams) {
            for (var key in urlParams) {
                if (urlParams[key] !== undefined) {
                    urlString += key + "=" + urlParams[key] + "&";
                }
            }
            if (urlString) {
                urlString = urlString.substr(0, urlString.length - 1);
            }
        }

        if (flags.pageReload) {
            location.search = urlString;
        }
        else {
            var fullpath = location.origin + location.pathname + "?" + urlString;
            history.pushState(null, "", fullpath);
        }

        if (callback) {
            callback();
        }
    },


    /**
     * Установить один из GET-параметров (после знака ?) в режиме дозаписи
     */
    setParam : function(key, value, flags, callback){
        var urlParams = Url.getParams();
        urlParams[key] = value;
        Url.setParams(urlParams, flags);
    },


    /**
     * Установить параметр filters_id
     * filterParams - объект
     */
    setFilters : function(filterParams){
        if (! filterParams) {
            filterParams = {};
        }
        var value = "";
        for (var key in filterParams) {
            if (filterParams[key] != undefined) {
                value += key + "_" + filterParams[key] + ",";
            }
        }
        value = value.substr(0, value.length - 1);

        if (value) {
            Url.setParam("filters_id", value);
        }
    },


    /**
     * Установка одного параметра в filters_id без удаления остальных параметров
     */
    setFilter : function(key, value){
        var filterParams = Url.getFilters();
        if (! filterParams) {
            filterParams = {};
        }
        filterParams[key] = value;
        Url.setFilters(filterParams);
    },


    /**
     * Установить параметры в урл после знака # (в блоке hash)
     * flags={}  параметры добавления
     * - append:true - дозапись к тем параметрам урла, которые уже есть
     */
    setHashes : function(urlParams, flags) {
        if (flags == undefined) flags = {};
        if (flags.append == undefined) flags.append = true;


        if (flags.append) {
            var hash = Url.getHashes();
            if (hash) {
                for (var key in hash) {
                    if (urlParams[key] == undefined) {
                        urlParams[key] = hash[key];
                    }
                }
            }
        }

        var urlString = "";
        if (urlParams) {
            for (var key in urlParams) {
                if (urlParams[key] !== undefined) {
                    urlString += key + "=" + urlParams[key] + "&";
                }
            }
            if (urlString) {
                urlString = urlString.substr(0, urlString.length - 1);
            }
        }
        location.hash = urlString;
    },


    /**
     * Установить один из GET-параметров (после знака #, в блоке hash) в режиме дозаписи
     */ 
    setHash : function(key, value){
        var urlParams = Url.getHashes();
        urlParams[key] = value;
        Url.setHashes(urlParams);
    },


    /**
     * Удалить ключ из хэша
     */
    devareHash : function(key) {
        var urlParams = Url.getHashes();
        if (urlParams[key]) {
            delete urlParams[key];
        }
        Url.setHashes(urlParams, {append: false});
    },


    /**
     * Установить параметр filters_id (в блоке hash)
     * filterParams - объект
     */
    setHashFilters : function(filterParams){
        if (! filterParams) {
            filterParams = {};
        }
        var value = "";
        for (var key in filterParams) {
            if (filterParams[key] != undefined) {
                value += key + "_" + filterParams[key] + ",";
            }
        }
        value = value.substr(0, value.length - 1);

        if (value) {
            Url.setHash("filters_id", value);
        }
    },


    /**
     * Установка одного параметра в filters_id без удаления остальных параметров (в блоке hash)
     */
    setHashFilter : function(key, value){
        var filterParams = Url.getHashFilters();
        if (! filterParams) {
            filterParams = {};
        }
        filterParams[key] = value;
        Url.setHashFilters(filterParams);
    },



    // ----------------------- Специфическая работа с фильтрами -----------------------

    /**
     * получить все значения из строки filters_id (в блоке hash)
     * возвращает сложный объект с массивами внутри (значений у одного ключа может ыбть несколько)
     */
    getMultipleHashFilters : function(){
        var filters_id = Url.getHash("filters_id");
        var filterObject = {};

        if (filters_id) {
            filters_id = decodeURI(filters_id);
            var filters = filters_id.split(',');
            var filter, f_a;
            var filterLength = filters.length;
            for (var f = 0; f < filterLength; f++) {
                filter = filters[f];

                f_a = filter.split('_');
                var fKey = f_a[0];
                var fValue = f_a[1];

                if (! filterObject[fKey]) {
                    filterObject[fKey] = [];
                }
                filterObject[fKey].push({
                    value : fValue
                });
            }
        }

        return filterObject;
    },


    /**
     * получить подтверждение, что ключ с таким значением в фильтрах - существует
     */
    isMultipleHashFilter : function(key, value){
        var filters_id = Url.getHash("filters_id");
        var filterObject = {};

        if (filters_id) {
            filters_id = decodeURI(filters_id);
            var filters = filters_id.split(',');
            var filter, f_a;
            var filterLength = filters.length;
            for (var f = 0; f < filterLength; f++) {
                filter = filters[f];

                f_a = filter.split('_');
                var fKey = f_a[0];
                var fValue = f_a[1];

                if (fKey == key && fValue == value) {
                    return true;
                }
            }
        }

        return false;
    },


    setMultipleHashFilters : function(filterParams) {
        if (! filterParams) {
            filterParams = {};
        }
        var value = "";
        for (var key in filterParams) {
            var filterBlock = filterParams[key];

            for (var i=0; i<filterBlock.length; i++) {
                value += key + "_" + filterBlock[i].value + ",";
            }
        }
        value = value.substr(0, value.length - 1);

        Url.setHash("filters_id", value);
    },

    /**
     * Установка параметра в filters_id даже в том случае, если такой ключ уже есть
     * Нельзя установить ключи с одинаковыми значениями!
     */
    setMultipleHashFilter : function(key, value){
        var filterParams = Url.getMultipleHashFilters();
        if (! filterParams) {
            filterParams = {};
        }

        if (filterParams[key]) {
            for (var i=0; i<filterParams[key].length; i++) {
                if (filterParams[key][i].value == value) {
                    // такое значение в filters_id уже есть
                    return;
                }
            }
        }
        else {
            filterParams[key] = [];
        }

        filterParams[key].push({
            value : value
        });
        Url.setMultipleHashFilters(filterParams);
    },

    /**
     * удалить ключ с таким значением из урла
     */
    devareMultipleHashFilter : function(key, value){
        var filterParams = Url.getMultipleHashFilters();
        if (! filterParams) {
            filterParams = {};
        }

        if (filterParams[key]) {
            for (var i=0; i<filterParams[key].length; i++) {
                if (filterParams[key][i].value == value) {
                    // такое значение в filters_id уже есть
                    filterParams[key].splice(i, 1);
                    break;
                }
            }
        }
        Url.setMultipleHashFilters(filterParams);
    },




    /**
     * Перегон объекта в строку
     */
    convertParamsToString : function(params) {
        var string = "";
        if (params) {
            for (var key in params) {
                if (params[key] !== undefined) {
                    string += key + "=" + params[key] + "&";
                }
            }
            if (string) {
                string = string.substr(0, string.length - 1);
            }
        }
        return string;
    }
}

