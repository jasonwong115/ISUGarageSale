(function () {
    var g, A = {}, u = {}, v = {}, I = [].slice,
        B = Array.isArray,
        i = function (a, b) {
            a || (a = {});
            for (var c in b)
                if (b.hasOwnProperty(c)) a[c] = b[c];
            return a
        }, s = function (a) {
            return typeof a == "function"
        }, w = function (a, b, c) {
            if (a != null)(a.forEach ? a : [a]).forEach(b, c)
        }, F = function (a, b, c) {
            for (var d in a) a.hasOwnProperty(d) && b.call(c, d, a[d])
        }, J = function (a, b, c) {
            var d = s(b) ? b() : b,
                f;
            c && w(c, function (j) {
                if (f = j(d)) d = f
            });
            return d
        }, K = function (a, b, c) {
            var d = a.prototype,
                f = function () {}, j = typeof b == "string" ? p(b) : b;
            i(a, j);
            f.prototype = j.prototype;
            a.superclass = f.prototype;
            a.prototype = new f;
            c && i(a.prototype, d);
            return d
        }, L = function (a, b, c) {
            if (!s(a)) {
                var d = a;
                a = d.init || function () {};
                i(a.prototype, d)
            }
            b && K(a, b, true);
            a.getName = a.getName || function () {
                return c
            };
            var f = a.prototype;
            f.constructor = a;
            f.getClass = function () {
                return a
            };
            return a
        }, W = function () {
            return this._ordinal
        }, M = function () {
            return this._name
        }, X = function (a) {
            return this._ordinal - a._ordinal
        }, q = function (a, b) {
            if (a.charAt(0) == "/") a = a.substring(1);
            if (a.charAt(0) == ".") {
                if (!b) return a;
                var c = b.split("/").slice(0, -1);
                w(a.split("/"), function (d) {
                    if (d == "..") c.splice(c.length - 1, 1);
                    else d != "." && c.push(d)
                });
                return c.join("/")
            } else return a.replace(/\./g, "/")
        }, p = function (a, b, c) {
            if (b) return p("raptor/loader").load(a, b, c);
            if (u.hasOwnProperty(a)) return u[a];
            if (g.exists(a)) {
                var d = A[a];
                return u[a] = J(a, d.factory, d.postCreate)
            } else throw Error(a + " not found");
        }, N = {
            load: function (a, b) {
                var c = this.normalize;
                B(a) || (a = [a]);
                for (var d = 0, f = a.length; d < f; d++) a[d] = c(a[d]);
                return p(a, b)
            },
            exists: function (a) {
                return g.exists(this.normalize(a))
            },
            find: function (a) {
                return g.find(this.normalize(a))
            }
        }, G = {
            extend: function () {
                return x(arguments, this.require, 0, 1)
            },
            Class: function () {
                return x(arguments, this.require, 1)
            },
            Enum: function () {
                return x(arguments, this.require, 0, 0, 1)
            }
        }, Y = function (a) {
            return i(a, G)
        }, O = function (a) {
            return i(a, N)
        }, x = function (a, b, c, d, f) {
            var j = 0,
                P = a.length - 1,
                Q, m, k, n, t, C = [],
                D, h, r = O(function (e, l) {
                    return l ? r.load(e, l) : b(e, k)
                }),
                y = new R(r),
                S = y.exports,
                Z = {
                    require: r,
                    exports: S,
                    module: y
                }, T = function () {
                    w(C, function (e, l) {
                        var z;
                        if (!(z = Z[e])) z = b(e,
                            k);
                        C[l] = z
                    });
                    return C
                };
            for (r.normalize = function (e) {
                return q(e, k)
            }; j < P; j++) {
                m = a[j];
                if (typeof m == "string")
                    if (k) n = q(m, k);
                    else k = y.id = q(m);
                    else if (B(m)) C = m;
                else if (f) t = m;
                else n = m.superclass
            }
            h = a[P];
            if (d) D = function (e) {
                if (s(h)) h = h.apply(g, T().concat([r, e]));
                if (h) i(s(e) ? e.prototype : e, h)
            };
            else {
                if (c || n) D = function (e) {
                    n = typeof n == "string" ? r(n) : n;
                    return L(e, n, k)
                };
                else if (f) {
                    if (B(h)) {
                        t = h;
                        h = null
                    }
                    D = function (e) {
                        if (e) {
                            if (typeof e == "object") e = L(e, 0, k)
                        } else e = function () {};
                        var l = e.prototype,
                            z = 0,
                            U = function (o, H) {
                                return i(e[o] =
                                    new H, {
                                        _ordinal: z++,
                                        _name: o
                                    })
                            };
                        if (B(t)) w(t, function (o) {
                            U(o, e)
                        });
                        else if (t) {
                            var V = function () {};
                            V.prototype = l;
                            F(t, function (o, H) {
                                e.apply(U(o, V), H || [])
                            })
                        }
                        e.valueOf = function (o) {
                            return e[o]
                        };
                        i(l, {
                            name: M,
                            ordinal: W,
                            compareTo: X
                        });
                        if (l.toString == Object.prototype.toString) l.toString = M;
                        return e
                    }
                }
                Q = s(h) ? function () {
                    var e = h.apply(g, T().concat([r, S, y]));
                    return e === undefined ? y.exports : e
                } : h
            }
            return g.define(k, Q, D)
        }, R = function (a) {
            var b = this;
            b.require = a;
            b.exports = {}
        };
    R.prototype = {
        logger: function () {
            var a = this;
            return a.l ||
                (a.l = p("raptor/logging").logger(a.id))
        }
    };
    g = {
        cache: u,
        inherit: K,
        extend: i,
        forEach: w,
        arrayFromArguments: function (a, b) {
            if (!a) return [];
            return b ? b < a.length ? I.call(a, b) : [] : I.call(a)
        },
        forEachEntry: F,
        createError: function (a, b) {
            var c, d = arguments.length,
                f = Error;
            if (d == 2) {
                c = a instanceof f ? a : new f(a);
                c._cause = b
            } else if (d == 1) c = a instanceof f ? a : new f(a);
            return c
        },
        define: function (a, b, c) {
            if (!a) return J.apply(g, arguments);
            var d = a && A[a] || (A[a] = {
                postCreate: []
            }),
                f;
            if (b) d.factory = b;
            if (c) {
                d.postCreate.push(c);
                if (f = u[a]) c(f)
            }
            if (typeof f ==
                "object" && f.toString === Object.prototype.toString) f.toString = function () {
                return "[" + a + "]"
            }
        },
        exists: function (a) {
            return A.hasOwnProperty(a)
        },
        find: function (a) {
            return g.exists(a) ? g.require(a) : undefined
        },
        require: p,
        normalize: q,
        _define: x,
        props: [N, G]
    };
    var E;
    if (typeof window != "undefined") {
        E = window;
        var $ = G.require = function (a, b) {
            return p(q(a, b))
        };
        define = Y(function () {
            return x(arguments, $)
        });
        require = O(function (a, b) {
            return s(b) ? require.load(a, b) : p(q(a))
        });
        require.normalize = q;
        define.amd = {}
    } else {
        E = global;
        module.exports =
            g
    }
    g.define("raptor", g);
    i(E, {
        $rset: function (a, b, c) {
            if (typeof b === "object") F(b, function (f, j) {
                $rset(a, f, j)
            });
            else {
                var d = v[a];
                d || (d = v[a] = {});
                if (c !== undefined) d[b] = c;
                else delete d[b]
            }
        },
        $radd: function (a, b) {
            var c = v[a];
            c || (c = v[a] = []);
            c.push(b)
        },
        $rget: function (a, b) {
            var c = v[a];
            return arguments.length == 2 ? c && c[b] : c
        }
    });
    g.global = E
})(); /*NEED*/
define("raptor/logging", ["raptor"], function (c) {
    var a = function () {
        return false
    }, b = {
            isDebugEnabled: a,
            isInfoEnabled: a,
            isWarnEnabled: a,
            isErrorEnabled: a,
            isFatalEnabled: a,
            dump: a,
            debug: a,
            info: a,
            warn: a,
            error: a,
            fatal: a,
            alert: a,
            trace: a
        };
    return {
        logger: function () {
            return b
        },
        makeLogger: function (d) {
            c.extend(d, b)
        },
        configure: a,
        voidLogger: b
    }
}); /*NEED*/
(function () {
    var c = require("raptor"),
        g, l = c.global,
        s = require("raptor/logging"),
        h = l.define || c.createDefine();
    l.raptor = g = c.extend({}, c);
    var t = /^(arrays|json.*|debug|listeners|loader.*|locale.*|logging|pubsub|objects|strings|templating.*|widgets)$/,
        m = function (a) {
            a = c.normalize(a);
            return t.test(a) ? "raptor/" + a : a
        }, u = function (a) {
            var b;
            return function () {
                return b ? b : b = s.logger(a)
            }
        }, k = function (a, b, e, i) {
            var f = a.length - 1,
                o = a[f],
                n = a[0];
            if (typeof a[0] != "string") n = "(anonymous)";
            if (typeof o == "function") a[f] = function () {
                var d =
                    o(g),
                    j = d;
                if (b || typeof d == "function") {
                    b = 1;
                    if (typeof d != "function") {
                        var p = d.init || function () {};
                        c.extend(p.prototype, d);
                        d = p
                    }
                    j = d.prototype
                }
                if (!i) j.logger = u(n);
                if (b) {
                    d.getName = function () {
                        return n
                    };
                    j.init = j.constructor = d
                }
                return d
            };
            return b ? h.Class.apply(h, a) : e ? h.Enum.apply(h, a) : h.apply(l, a)
        }, q = function () {
            return k(arguments)
        }, r = function (a) {
            return c.find(m(a))
        };
    c.extend(g, {
        require: function (a, b, e, i) {
            return i ? g.find(a) : c.require(typeof a === "string" ? m(a) : a.map(m), b, e)
        },
        find: r,
        load: r,
        define: q,
        defineModule: q,
        defineClass: function () {
            return k(arguments, 1)
        },
        defineEnum: function () {
            return k(arguments, 0, 1)
        },
        defineMixin: function () {
            return k(arguments, 0, 0, 1)
        },
        extend: function (a, b) {
            if (typeof a === "string") {
                if (typeof b === "function") {
                    var e = b;
                    b = function (i, f) {
                        if (typeof f === "function") f = f.prototype;
                        return e(g, f)
                    }
                }
                h.extend(a, b)
            } else return c.extend(a, b)
        },
        inherit: function (a, b, e) {
            c.inherit(a, typeof b === "string" ? g.require(b) : b, e)
        },
        isString: function (a) {
            return typeof a == "string"
        },
        isNumber: function (a) {
            return typeof a === "number"
        },
        isFunction: function (a) {
            return typeof a == "function"
        },
        isObject: function (a) {
            return typeof a == "object"
        },
        isBoolean: function (a) {
            return typeof a === "boolean"
        },
        isServer: function () {
            return !this.isClient()
        },
        isClient: function () {
            return typeof window !== undefined
        },
        isArray: Array.isArray
    })
})(); /*NEED*/