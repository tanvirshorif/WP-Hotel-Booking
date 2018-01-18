;

/**
 * Helpers
 */
(function (exports) {
    function cloneObject(object) {
        return JSON.parse(JSON.stringify(object));
    }

    exports.WPHB_Helpers = {
        cloneObject: cloneObject
    };
})(window);