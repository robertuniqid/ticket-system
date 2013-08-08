LayoutHelper.Hash = new function () {
    var params;

    this.fetchParams = function () {
        return params;
    };

    this.set_silent = function (key, value) {
        params[key] = value;
    };

    this.set = function (key, value) {
        params[key] = value;
        this.push();
    };

    this.remove_silent = function (key) {
        delete params[key];
    };

    this.remove = function (key, value) {
        delete params[key];
        this.push();
    };


    this.get = function (key) {
        return params[key];
    };

    this.keyExists = function (key) {
        return params.hasOwnProperty(key);
    };

    this.push= function () {
        var hashBuilder = [], key, value;

        for(key in params) if (params.hasOwnProperty(key)) {
            if(typeof params[key] != "undefined") {
                key = LayoutHelper.EncodeUrl(key);

                value = LayoutHelper.EncodeUrl(params[key]);

                hashBuilder.push(key + ( (value !== "undefined") ? '=' + value : "" ));
            }
        }

        var hash = $.trim(hashBuilder.join("&"));

        if(hash.charAt(0) == '&')
            hash = hash.substring(1);

        window.location.hash = hash;
    };

    this.refresh = function(){
        params = {}
        var hashStr = window.location.hash, hashArray, keyVal
        hashStr = hashStr.substring(1, hashStr.length);
        hashArray = hashStr.split('&');

        for(var i = 0; i < hashArray.length; i++) {
            keyVal = hashArray[i].split('=');
            params[decodeURIComponent(keyVal[0])] = (typeof keyVal[1] != "undefined") ? decodeURIComponent(keyVal[1]) : keyVal[1];
        }
    };

    (this.load = function () {
        params = {}
        var hashStr = window.location.hash, hashArray, keyVal
        hashStr = hashStr.substring(1, hashStr.length);
        hashArray = hashStr.split('&');

        for(var i = 0; i < hashArray.length; i++) {
            keyVal = hashArray[i].split('=');
            params[decodeURIComponent(keyVal[0])] = (typeof keyVal[1] != "undefined") ? decodeURIComponent(keyVal[1]) : keyVal[1];
        }
    })();
}