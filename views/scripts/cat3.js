(function (b, j) {
    var u = function (g, a) {
        var e = this,
            h = b.isNumeric(a.width) ? a.width + "px" : "auto",
            w = b.isNumeric(a.height) ? a.height + "px" : "auto";
        h = "<div class='dlg' style='" + ["width:" + h, "height:" + w, "display:none", "z-index:" + a.zIndex, a.extraStyle ? a.extraStyle : ""].join(";") + "'></div>";
        var d = b(h),
            m = d.outerWidth(),
            n = b(a.outerBox),
            k = false,
            p = a.modal ? b.mask(a.maskConfig) : null,
            q = b(j);
        n.keyup(function (c) {
            if (c.keyCode == 27) {
                if (cfg.autocomplete) {
                    cfg.autocomplete.block();
                    cfg.autocomplete.hide()
                }
                e.hide()
            }
        });
        var x = function () {
            n.append(d);
            d.append(g)
        }, A = function (c) {
                var i = c.target,
                    f;
                if (f = !b.contains(d[0], i)) {
                    if (f = k) {
                        var l = c;
                        f = d.offset().left;
                        var r = d.offset().top,
                            y = f + d.width(),
                            z = r + d.height(),
                            s = l.clientX,
                            t = l.clientY + q.scrollTop();
                        f = !(s >= f && s <= y && t >= r && t <= z)
                    }
                    f = f
                }
                if (f) {
                    b("body").trigger("DIALOG_HIT_OUTSIDE");
                    e.hide()
                }
            };
        b.extend(e, {
            show: function (c) {
                k = true;
                if (a.clzWhenClickOutside && c && c.target) {
                    c.preventDefault();
                    c.stopPropagation()
                }
                d.css("display", "block");
                g.css("display", "block");
                a.modal && p.show({
                    parent: n
                });
                a.clzWhenClickOutside && b("body").on("click",
                    A);
                d.focus();
                e.adjustPosition();
                b("body").css("overflow", "hidden")
            },
            hide: function (c, i) {
                k = false;
                d.css("display", "none");
                a.modal && p.hide();
                a.closeHandler && !i && a.closeHandler(c);
                b("body").css("overflow", "visible")
            },
            hideWithoutClzMask: function (c) {
                k = false;
                a.hideHandler && a.hideHandler(c);
                d.css("display", "none")
            },
            close: function () {
                k = false;
                d.remove();
                a.closeHandler && a.closeHandler()
            },
            fadeOut: function () {
                k = false;
                d.fadeOut(400, function () {
                    d.remove()
                });
                a.closeHandler && a.closeHandler()
            },
            getDialog: function () {
                return d
            },
            getContent: function () {
                return g
            },
            isShowed: function () {
                return k
            },
            adjustPosition: function () {
                var c = (b(j).width() - m) / 2,
                    i = (b(j).height() - d.outerHeight()) / 2,
                    f = b(document).scrollTop(),
                    l = b(document).scrollLeft();
                d.css({
                    position: "fixed",
                    left: c,
                    top: i,
                    width: m
                });
                b("body").css("overflow", "hidden");
                m > b(j).width() && d.css({
                    position: "absolute",
                    top: f + i,
                    left: l,
                    width: "100%"
                });
                if (d.outerHeight() > b(j).height()) {
                    d.css({
                        position: "absolute",
                        top: f,
                        left: l + c
                    });
                    b("body").css("overflow", "visible")
                }
                m > b(j).width() && d.outerHeight() >
                    b(j).height() && d.css({
                        position: "absolute",
                        top: f,
                        left: l,
                        width: "100%"
                    })
            }
        });
        q.on("resize", function () {
            e.isShowed() && e.adjustPosition()
        });
        x();
        if (a.hasClzBtn) {
            h = b("<button type='button' class='clz' tabindex='1'>&times;</button>");
            d.prepend(h);
            h.on("keydown click", function (c) {
                var i = c.keyCode ? c.keyCode : c.which;
                if (c.type == "keydown" && i != 13) return true;
                c.preventDefault();
                b("body").trigger("DIALOG_CLICK_CLOSE");
                e.hide(c)
            });
            a.clzBtnSelector && b(a.clzBtnSelector).on("click", function (c) {
                b("body").trigger("DIALOG_CLICK_CLOSE");
                e.hide(c)
            })
        }
    }, v = {
            width: 300,
            height: 80,
            hasClzBtn: true,
            clzWhenClickOutside: true,
            clzBtnSelector: null,
            modal: false,
            maskConfig: null,
            outerBox: "body",
            zIndex: 2E5,
            content: null,
            hideHandler: null,
            closeHandler: null
        };
    b.fn.dialog = function (g) {
        if (this.length === 0) return null;
        var a = b.extend({}, v, g);
        a = new u(this, a);
        var e = this.attr("id");
        e && b.dialogManager.addDialog(e, a);
        return a
    };
    b.dialog = function (g) {
        var a = b.extend({}, v, g);
        a = new u(g.content, a);
        var e = g.content.attr("id");
        e && b.dialogManager.addDialog(e, a);
        return a
    };
    var o =
        null,
        B = function () {
            var g = this,
                a = {};
            b.extend(g, {
                getDialog: function (e) {
                    return a[e]
                },
                addDialog: function (e, h) {
                    a[e] = h
                }
            })
        };
    if (o === null) o = new B;
    b.dialogManager = o
})(jQuery, window); /*NEED*/
raptor.defineClass("raptor.tracking.idmap.IdMap", function (d) {
    var f = d.require("isugaragesale.cookies"),
        g = function () {};
    d.extend(g, {
        roverService: function (a) {
            var b = this;
            b.url = $uri(a || "");
            b.url.protocol.match(/https/) || f.readCookie("dp1", "idm") || d.bind(b, window, "load", b.sendRequest)
        },
        sendRequest: function () {
            this.url.appendParam("cb", "raptor.require('raptor.tracking.idmap.IdMap').handleResponse");
            $.ajax({
                url: this.url.getUrl(),
                dataType: "jsonp",
                jsonp: false
            })
        },
        handleResponse: function (a) {
            var b = this;
            b.image = $("<img/>").css("display",
                "none");
            for (var c = 0, e = a.length - 1; c < e; c++) a[c] && b.image.attr("src", a[c]);
            e && b.setCookieExpiration(a[e])
        },
        setCookieExpiration: function (a) {
            typeof a == "number" && a > 0 && f.writeCookielet("dp1", "idm", "1", a / 86400, "")
        }
    });
    return g
}); /*NEED*/
(function (a) {
    a(document).ready(function () {
        var f = a(".topnav"),
            D = f.find(".section"),
            t = f.find(".topnavlayer"),
            k = f.find("a"),
            l = f.find(".cat, .scat"),
            E = f.find(".expand"),
            n = null,
            u = false,
            v = false,
            m = 200,
            w = a.messaging(),
            F = window.location.hash,
            I = function () {
                f.find("table").mouseenter(function () {
                    m = 10
                }).mouseleave(function () {
                    m = 250
                });
                f.hasClass("lazyloading") && l.one("focusin mouseover", function () {
                    var b = a(this).find("img"),
                        c, d, e;
                    for (d = 0; d < b.length; d++) {
                        c = b.eq(d);
                        (e = c.data("xrc")) && c.attr("src", e)
                    }
                });
                var g = function (b,
                    c) {
                    var d = k.index(b),
                        e = (d + c) % k.length,
                        o = k.eq(e);
                    return o
                }, h = function (b, c, d) {
                        var e = g(b, 1);
                        if (b.closest(".cat").hasClass("today"))
                            if ((e.hasClass("rt") || e.hasClass("expand")) && d > 0) {
                                setTimeout(function () {
                                    h(b, c, --d)
                                }, c);
                                return
                            }
                        setTimeout(function () {
                            e.focus()
                        }, c)
                    }, q = function () {
                        var b = {};
                        if (a.feedContext.sitespeedImages) b.FEED_SITESPEED_IMAGES = 1;
                        if (a.feedContext.sitespeedImagesZoom) b.FEED_SITESPEED_IMAGES_ZOOM = 1;
                        if (a.feedContext.sitespeedImagesZoomProg) b.FEED_SITESPEED_IMAGES_ZOOM_PROGRESSIVE = 1;
                        if (a.feedContext.enableWebP) b.FEED_ENABLE_WEBP =
                            1;
                        if (a.feedContext.domainShardingOn) b.FEED_SITESPEED_DOMAIN_SHARDING = a.feedContext.domainShardingValue;
                        a.ajax({
                            url: "/_feedhome/ws/collectionFlyout",
                            data: b,
                            cache: false,
                            success: function (c) {
                                if (c && c.length > 0) {
                                    var d = f.find("td.today .content");
                                    d.html(c);
                                    d.find(".colList a.hoverable").on("mouseover", function () {
                                        var e = a(this),
                                            o = e.data("image-alpha"),
                                            x = e.data("image-beta"),
                                            y = e.data("image-gamma"),
                                            G = e.data("url"),
                                            z = e.closest(".data"),
                                            i = z.find(".imgs"),
                                            H = i.find(".link");
                                        if (e.closest("li").hasClass("active")) return false;
                                        z.find("li").removeClass("active");
                                        e.closest("li").addClass("active");
                                        var p = i.find(".alpha");
                                        p.find("img").remove();
                                        p.html('<img data-fit="true" />');
                                        p.find("img").attr("data-onload-src", o);
                                        var A = i.find(".beta img");
                                        if (x) {
                                            A.remove();
                                            i.find(".beta").html('<img data-fit="true"/>');
                                            i.find(".beta img").attr("data-onload-src", x)
                                        } else A.hide();
                                        var B = i.find(".gamma img");
                                        if (y) {
                                            B.remove();
                                            i.find(".gamma").html('<img data-fit="true"/>');
                                            i.find(".gamma img").attr("data-onload-src", y)
                                        } else B.hide();
                                        H.attr("href",
                                            G);
                                        a.LazyImage.load(i, "onload-src")
                                    });
                                    d.find(".signin").on("click", function (e) {
                                        e.preventDefault();
                                        window.location.href = a.feedContext.signInURL;
                                        return false
                                    });
                                    k = f.find("a");
                                    a.LazyImage.load(".section.today.cat", "onload-src")
                                }
                            }
                        })
                    }, j = function (b) {
                        b.preventDefault();
                        var c = a(this),
                            d = c.closest(".cat");
                        if (d.hasClass("today") && !d.data("loaded")) {
                            d.data("loaded", true);
                            q()
                        }
                        clearTimeout(v);
                        u = window.setTimeout(function () {
                            n && n.removeClass("show");
                            d.addClass("show");
                            n = d
                        }, m);
                        c.hasClass("expand") && h(c, m, 20)
                    }, r = function () {
                        window.clearTimeout(u);
                        v = window.setTimeout(function () {
                            l.removeClass("show")
                        }, 10)
                    }, s = function (b) {
                        var c = a(b.target);
                        c.is(".rt, .expand") && r()
                    };
                l.on("mouseenter", j).on("mouseleave", r).on("focusin", s);
                E.on("click", j).on("focus", function () {
                    a(this).parent().css("top", "30px")
                }).on("focusout", function () {
                    a(this).parent().removeAttr("style")
                })
            };
        !a("body").hasClass("touch") && !a("body").hasClass("sz600") && I();
        var J = function () {
            var g = [],
                h = [],
                q = f.offset().left + f.outerWidth(),
                j;
            l.each(function (r, s) {
                var b = a(s);
                if (b.offset().left + b.outerWidth() >
                    q) h.push(b);
                else b.hasClass("more") || g.push(b)
            });
            if (h.length) {
                for (j = 0; j < h.length; j++) h[j].hide();
                g[g.length - 1].hide();
                f.find(".more").show()
            } else f.find(".more").hide()
        }, K = t.dialog({
                width: a("body").width() - 200,
                height: "auto",
                outerBox: "body",
                extraStyle: "border: none; z-index: 9999999",
                clzWhenClickOutside: true,
                hasClzBtn: true,
                modal: true
            });
        f.on("click", ".more", function (g) {
            g.preventDefault();
            K.show();
            return false
        });
        t.on("click", "h3.clickable", function (g) {
            g.preventDefault();
            document.location = a(this).find("a").attr("href");
            return false
        });
        var C = function () {
            a(".alert").hide();
            a("#fixed-feed-tiles").hide();
            var g = a.feedService({
                callback: function (h) {
                    w.notify("LOAD_POP_FEED", h);
                    w.notify("RESUME_RESPONSE_TO_PAGE_SCROLL");
                    D.removeClass("active");
                    f.find("td.featured").addClass("active")
                },
                userId: 0
            });
            g.getFeeds()
        };
        f.on("click", ".featured", function (g) {
            g.preventDefault();
            C()
        });
        a("#emptyfeedmsg").on("click", ".featured", function (g) {
            g.preventDefault();
            C()
        });
        F == "#featured" && a(".featured", f).click();
        J()
    })
})(jQuery); /*NEED*/
(function (g) {
    var f = null,
        j = function () {
            var i = this,
                e = [];
            g.extend(i, {
                register: function (c, d) {
                    var a = e[c];
                    if (a === null || a === undefined) a = [];
                    a.push(d);
                    e[c] = a
                },
                unregister: function (c, d) {
                    if (d) {
                        var a = e[c],
                            b = 0;
                        if (a && a.length > 0)
                            for (; b < a.length; b++)
                                if (d == a[b]) {
                                    delete a[b];
                                    break
                                }
                    } else delete e[c]
                },
                notify: function (c, d) {
                    var a = e[c],
                        b = 0;
                    if (a && a.length > 0)
                        for (; b < a.length; b++) {
                            var h = a[b];
                            typeof h == "function" && h(d)
                        }
                }
            })
        };
    g.messaging = function () {
        if (f === null) f = new j;
        return f
    }
})(jQuery, window); /*NEED*/