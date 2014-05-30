MEERKAT = {
    removeClassWithPrefix: function (el, prefix) {
        var classes = $(el).attr("class").split(" ").filter(function(c) {
            return c.lastIndexOf('js_', 0) !== 0;
        });
        $(el).attr("class", classes.join(" "));
    }
}