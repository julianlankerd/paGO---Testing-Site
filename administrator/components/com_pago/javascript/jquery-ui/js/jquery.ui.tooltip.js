(function (f) {
    var g = 0;
    f.widget("ui.tooltip", {
        options: {
            items: "[title]",
            content: function () {
                return f(this).attr("title")
            },
            position: {
                my: "left bottom-20",
                at: "left",
                offset: "0 0"
            }
        },
        _create: function () {
            var b = this;
            this.tooltip = f("<div></div>").attr("id", "ui-tooltip-" + g++).attr("role", "tooltip").attr("aria-hidden", "true").addClass("ui-tooltip ui-widget ui-corner-all ui-widget-content").appendTo(document.body).hide();
            this.tooltipContent = f("<div></div>").addClass("ui-tooltip-content").appendTo(this.tooltip);
            this.opacity = this.tooltip.css("opacity");
            this.element.bind("focus.tooltip mouseover.tooltip", function (a) {
                b.open(a)
            }).bind("blur.tooltip mouseout.tooltip", function (a) {
                b.close(a)
            })
        },
        enable: function () {
            this.options.disabled = false
        },
        disable: function () {
            this.options.disabled = true
        },
        destroy: function () {
            this.tooltip.remove();
            f.Widget.prototype.destroy.apply(this, arguments)
        },
        widget: function () {
            return this.element.pushStack(this.tooltip.get())
        },
        open: function (b) {
            var c = f(b && b.target || this.element).closest(this.options.items);
            if (this.current && this.current[0] == c[0]) return;
            var d = this;
            this.current = c;
            this.currentTitle = c.attr("title");
            var e = this.options.content.call(c[0], function (a) {
                setTimeout(function () {
                    if (d.current == c) d._show(b, c, a)
                }, 13)
            });
            if (e) {
                d._show(b, c, e)
            }
        },
        _show: function (a, b, c) {
            if (!c) return;
            b.attr("title", "");
            if (this.options.disabled) return;
            this.tooltipContent.html(c);
            this.tooltip.css({
                top: 0,
                left: 0
            }).show().position(f.extend({
                of: b
            }, this.options.position)).hide();
            this.tooltip.attr("aria-hidden", "false");
            b.attr("aria-describedby", this.tooltip.attr("id"));
            this.tooltip.stop(false, true).fadeIn();
            this._trigger("open", a)
        },
        close: function (a) {
            if (!this.current) return;
            var b = this.current;
            this.current = null;
            b.attr("title", this.currentTitle);
            if (this.options.disabled) return;
            b.removeAttr("aria-describedby");
            this.tooltip.attr("aria-hidden", "true");
            this.tooltip.stop(false, true).fadeOut();
            this._trigger("close", a)
        }
    })
})(jQuery);;