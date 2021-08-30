
window.Application = {

    Components: {},

    /**
     * Front controller application, init all plugin
     * and event handler
     */

    addComponent: function(name, object) {
        console.debug('Add component');

        if (name instanceof Array) {
            var self = this;
            name.forEach(function (element, index, array) { self.Components[element] = object; });
        } else {
            this.Components[name] = object;
        }

        object.run();

        if(object.resizeFunctions != null && typeof(object.resizeFunctions) == "function") {
            $(window).on("resize", function() {
                object.resizeFunctions();
            });
        };

        if(object.scrollFunctions != null && typeof(object.scrollFunctions) == "function") {
            $(window).on("scroll", function() {
                object.scrollFunctions();
            });
        };

        if(object.loadFunctions != null && typeof(object.loadFunctions) == "function") {
            $(window).on("load", function() {
                object.loadFunctions();
            });
        }
    }
};

