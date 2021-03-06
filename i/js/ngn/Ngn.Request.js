Ngn.Request = new Class({
  Extends: Request,

  success: function(text, xml) {
    if (text.contains('Error: ')) {
      return;
      new Ngn.Dialog.Error({
        width: 600,
        message: 'Ошибка запроса: ' + this.options.url
      });
    }
    this.parent(text, xml);
  }

});

Ngn.Request.Loading = new Class({
  Extends: Ngn.Request,

  success: function(text, xml) {
    Ngn.loading(false);
    this.parent(text, xml);
  },

  send: function(options) {
    Ngn.loading(true);
    this.parent(options);
  }

});

Ngn.JSON = {};
Ngn.JSON.decode = function(text, secure) {
  return Ngn.JSON.process(JSON.decode(text, secure));
};

Ngn.JSON.process = function(json) {
  if (!json) return json;
  for (var i in json) {
    if (typeof(json[i]) == 'object' || typeof(json[i]) == 'array') {
      json[i] = Ngn.JSON.process(json[i]);
    } else if (typeOf(json[i]) == 'string') {
      if (json[i].test(/^func: .*/)) {
        json[i] = json[i].replace(/^func: (.*)/, '$1');
        json[i] = eval('(function() {' + json[i] + '})');
      }
    }
  }
  return json;
};

Ngn.Request.JSON = new Class({
  Extends: Request.JSON,

  success: function(text) {
    this.response.json = Ngn.JSON.decode(text, this.options.secure);
    if (this.response.json === null) {
      this.onSuccess({});
      return;
    }
    if (this.response.json.sflJsDeltaUrl) {
      Asset.javascript(this.response.json.sflJsDeltaUrl, {
        onLoad: function() {
          if (Ngn.Request.sflJsDeltaUrlOnLoad) {
            Ngn.Request.sflJsDeltaUrlOnLoad();
            Ngn.Request.sflJsDeltaUrlOnLoad = false;
          }
        }
      });
    }
    if (this.response.json.sflCssDeltaUrl) Asset.css(this.response.json.sflCssDeltaUrl);
    if (this.response.json.actionDisabled) {
      window.location.reload(true);
      return;
    }
    if (this.response.json.error) {
      c(this.response.json);
      Ngn.Request.JSON.throwServerError(this.response.json);
      return;
    }
    this.onSuccess(this.response.json, text);
  },

  failure: function(xhr) {
    //new Ngn.Dialog.Error({message: this.xhr.responseText + '<hr/>URL: ' + this.options.url});
    this.parent();
  }

});

Ngn.Request.JSON.throwServerError = function(r) {
  throw new Error(r.error.message + "\n----------\n" + r.error.trace)
};

Ngn.Request.sflJsDeltaUrlOnLoad = false;