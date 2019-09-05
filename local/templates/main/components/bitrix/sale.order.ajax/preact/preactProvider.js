!function(preact) {
    'use strict';
    function extendComponent(object) {
        function extended() {
            preact.Component.apply(this, arguments);

            for (var i in object) {
                if (i !== 'render' && typeof object[i] === 'function') {
                    this[i] = object[i].bind(this);
                }
            }

            if (object.init) {
                object.init.apply(this, arguments);
            }
        }

        extended.prototype = Object.assign(Object.create(preact.Component.prototype), object);

        extended.prototype.constructor = extended;

        return extended;
    }
    function render(component, container, props) {
        preactProvider.view[component.name] = preactProvider.view[component.name] || [];

        var entity = preact.render(
            preact.h(extendComponent({
                init: function (props) {
                    this.state = props;
                },
                render: function (props, state) {
                    return preact.h(component, state)
                }
            }), props), container, (container.children[0]) ? container.children[0] : null
        );

        preactProvider.view[component.name].push(entity._component);
    }
    var preactProvider = {
        view: {},
        extendComponent: extendComponent,
        render: render
    };
    if ('undefined' != typeof module) module.exports = preactProvider; else self.preactProvider = preactProvider;
}(preact);