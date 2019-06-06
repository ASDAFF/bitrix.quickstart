var Ml2WebForms_default = {
    webform: null,
    error: false,
    webform_id: '',

    // init webform script
    init: function() {
        var self = this;
        self.webform = document.getElementById("ml2webforms_" + self.webform_id);
    },

    showResult: function(result) {
        var self = this;

        // add form result message block if not exists
        if (!self.webform.querySelector('.message')) {
            var msg = document.createElement('div');
            msg.className = "message";
            self.webform.insertBefore(msg, self.webform.firstChild);
        }

        // show form result message
        self.webform.querySelector('.message').innerHTML = result.message;

        // if form result success show message and hide errors
        if (result.status == 'success') {
            self.webform.querySelector('.message').className = self.webform.querySelector('.message').className.replace(' error', '');
            for (var i = 0; i < self.webform.elements.length; i++) {
                self.webform.elements[i].parentNode.className = self.webform.elements[i].parentNode.className.replace(' error', '');
                self.webform.elements[i].parentNode.title = '';
            }
            self.webform.reset();
        // if form result fail show errors
        } else if (result.status == 'failure') {
            self.webform.querySelector('.message').className += self.webform.querySelector('.message').className.indexOf(' error') != -1 ? '' : ' error';
            for (var i = 0; i < self.webform.elements.length; i++) {
                self.webform.elements[i].parentNode.className = self.webform.elements[i].parentNode.className.replace(' error', '');
                self.webform.elements[i].parentNode.title = '';
                if (result.errors[self.webform.elements[i].name]) {
                    self.webform.elements[i].parentNode.className += self.webform.elements[i].parentNode.className.indexOf(' error') != -1 ? '' : ' error';
                    var errorText = '';
                    for (var j = 0; j < result.errors[self.webform.elements[i].name].length; j++) {
                        errorText += result.errors[self.webform.elements[i].name][j][1] + '\r\n';
                    }
                    self.webform.elements[i].parentNode.title = errorText;
                }
            }
        }
    }
};
