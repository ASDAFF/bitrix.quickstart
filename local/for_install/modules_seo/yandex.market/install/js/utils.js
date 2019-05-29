(function(BX, $, window) {

	var utils = BX.namespace('YandexMarket.Utils');

	$.extend(true, utils, {

		registerLang: function(collection, prefix) {
			var key;
			var bxCollection;

			if (prefix) {
				bxCollection = {};

				for (key in collection) {
					if (collection.hasOwnProperty(key)) {
						bxCollection[prefix + key] = collection[key];
					}
				}

				BX.message(bxCollection);
			} else {
				BX.message(collection);
			}
		},

		sklon: function(number, titles) {
			var cases = [2, 0, 1, 1, 1, 2];
			return titles[ (number%100>4 && number%100<20) ? 2 : cases[Math.min(number%10, 5)] ];
		},

		compileTemplate: function(template, replaces) {
			var replaceKey;
			var replaceValue;
			var result = template;

			for (replaceKey in replaces) {
				if (replaces.hasOwnProperty(replaceKey)) {
					replaceValue = replaces[replaceKey];

					result = result.replace('#' + replaceKey + '#', replaceValue);
				}
			}

			return result;
		},

		inherit: function(parent, child, protoProps, staticProps) {
            this.extend(true, child, parent, staticProps);

            var Surrogate = function() {};
            Surrogate.prototype = parent.prototype;
            child.prototype = new Surrogate();

            if (protoProps) this.extend(true, child.prototype, protoProps);

            child.prototype.constructor = child;
            child.prototype.__super__ = parent.prototype;

            return child;
        },

		extend: function() {
            var src, copyIsArray, copyIsObject, copy, name, options, clone, target = arguments[0] || {},
                i = 1,
                length = arguments.length,
                deep = false,
                hasOwn;

            // Handle a deep copy situation
            if (typeof target === "boolean") {
                deep = target;

                // skip the boolean and the target
                target = arguments[i] || {};
                i++;
            }

            // Handle case when target is a string or something (possible in deep copy)
            if (typeof target !== "object" && !$.isFunction(target)) {
                target = {};
            }

            // extend jQuery itself if only one argument is passed
            if (i === length) {
                target = this;
                i--;
            }

            for (; i < length; i++) {
                // Only deal with non-null/undefined values
                if ((options = arguments[i]) != null) {
                    // Extend the base object
                    for (name in options) {
                        src = target[name];
                        copy = options[name];

                        // Prevent never-ending loop
                        if (target === copy) {
                            continue;
                        }

                        copyIsObject = $.isPlainObject(copy);
                        copyIsArray = $.isArray(copy);

                        // Recurse if we're merging plain objects or arrays
                        if (deep && copyIsObject) {
                            hasOwn = target.hasOwnProperty(name);
                            clone = src && $.isPlainObject(src) ? (hasOwn ? src : this.extend(true, {}, src)) : {};

                            // Never move original objects, clone them
                            target[name] = this.extend(deep, clone, copy);

                            // Don't bring in undefined values
                        } else if (copy !== undefined) {
                            target[name] = copy;
                        }
                    }
                }
            }

            // Return the modified object
            return target;
        },

        debounce: function(fn, delay, context) {
            var debouncedFn = BX.debounce(fn, delay, context);

            debouncedFn.guid = fn.guid = fn.guid || $.guid++;

            return debouncedFn;
        }

	});

})(BX, jQuery, window);