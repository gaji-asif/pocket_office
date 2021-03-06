! function (AV, window, document) {
    AV.build = {
        contentShouldUseStaging: !1,
        version: "4.2.1",
        bundled: !1,
        feather_baseURL: "http://feather.aviary.com/csdk/4.2.1.25/",
        feather_baseURL_SSL: "https://dme0ih8comzn4.cloudfront.net/csdk/4.2.1.25/",
        feather_stickerURL: "http://feather.aviary.com/stickers/",
        feather_stickerURL_SSL: "https://dme0ih8comzn4.cloudfront.net/stickers/",
        imgrecvBase: "http://featherservices.aviary.com/",
        imgrecvBase_SSL: "https://featherservices.aviary.com/",
        featherTargetAnnounce: "http://featherservices.aviary.com/feather_target_announce_v3.html",
        featherTargetAnnounce_SSL: "https://featherservices.aviary.com/feather_target_announce_v3.html",
        imgrecvServer: "http://featherservices.aviary.com/FeatherReceiver.aspx",
        imgrecvServer_SSL: "https://featherservices.aviary.com/FeatherReceiver.aspx",
        jsonp_imgserver: "http://featherservices.aviary.com/imgjsonpserver.aspx",
        jsonp_imgserver_SSL: "https://featherservices.aviary.com/imgjsonpserver.aspx",
        proxyServer: "http://featherservices.aviary.com/proxy.aspx",
        proxyServer_SSL: "https://featherservices.aviary.com/proxy.aspx",
        asyncImgrecvBase: "http://cc-api-aviary-cds.adobe.io/",
        asyncImgrecvBase_SSL: "https://cc-api-aviary-cds.adobe.io/",
        manifestURL: "http://cd.aviary.com",
        manifestURL_SSL: "https://d42hh4005hpu.cloudfront.net",
        gatewayAssetURL: "http://cc-api-aviary-cds.adobe.io",
        gatewayAssetURL_SSL: "https://cc-api-aviary-cds.adobe.io",
        cdsContentURL: "http://cd.aviary.com",
        cdsContentURL_SSL: "https://d42hh4005hpu.cloudfront.net",
        asyncFeatherTargetAnnounce: "http://cc-api-aviary-cds.adobe.io/feather_target_announce_v3.html",
        asyncFeatherTargetAnnounce_SSL: "https://cc-api-aviary-cds.adobe.io/feather_target_announce_v3.html",
        asyncImgrecvCreateJob: "http://cc-api-aviary-cds.adobe.io/v2/createjob",
        asyncImgrecvCreateJob_SSL: "https://cc-api-aviary-cds.adobe.io/v2/createjob",
        asyncImgrecvGetJobStatus: "http://cc-api-aviary-cds.adobe.io/v2/getjobstatus",
        asyncImgrecvGetJobStatus_SSL: "https://cc-api-aviary-cds.adobe.io/v2/getjobstatus",
        googleTracker: "UA-84575-22",
        inAppPurchaseFrameURL: "http://purchases.viary.com/gateway.aspx?p=flickr"
    };
    var eventSplitter = /\s+/,
        Events = AV.Events = {
            on: function (e, t, n) {
                var a, o, i;
                if (!t) return this;
                for (e = e.split(eventSplitter), a = this._callbacks || (this._callbacks = {}) ; o = e.shift() ;) i = a[o] || (a[o] = []), i.push(t, n);
                return this
            },
            off: function (e, t, n) {
                var a, o, i, r;
                if (!(o = this._callbacks)) return this;
                if (!(e || t || n)) return delete this._callbacks, this;
                for (e = e ? e.split(eventSplitter) : _.keys(o) ; a = e.shift() ;)
                    if ((i = o[a]) && (t || n))
                        for (r = i.length - 2; r >= 0; r -= 2) t && i[r] !== t || n && i[r + 1] !== n || i.splice(r, 2);
                    else delete o[a];
                return this
            },
            trigger: function (e) {
                var t, n, a, o, i, r, s, l;
                if (!(n = this._callbacks)) return this;
                for (l = [], e = e.split(eventSplitter), o = 1, i = arguments.length; i > o; o++) l[o - 1] = arguments[o];
                for (; t = e.shift() ;) {
                    if ((s = n.all) && (s = s.slice()), (a = n[t]) && (a = a.slice()), a)
                        for (o = 0, i = a.length; i > o; o += 2) a[o].apply(a[o + 1] || this, l);
                    if (s)
                        for (r = [t].concat(l), o = 0, i = s.length; i > o; o += 2) s[o].apply(s[o + 1] || this, r)
                }
                return this
            }
        };
    "undefined" == typeof AV && (AV = {}), AV.JSON = {},
        function () {
            "use strict";

            function f(e) {
                return 10 > e ? "0" + e : e
            }

            function quote(e) {
                return escapable.lastIndex = 0, escapable.test(e) ? '"' + e.replace(escapable, function (e) {
                    var t = meta[e];
                    return "string" == typeof t ? t : "\\u" + ("0000" + e.charCodeAt(0).toString(16)).slice(-4)
                }) + '"' : '"' + e + '"'
            }

            function str(e, t) {
                var n, a, o, i, r, s = gap,
                    l = t[e];
                switch (l && "object" == typeof l && "function" == typeof l.toJSON && (l = l.toJSON(e)), "function" == typeof rep && (l = rep.call(t, e, l)), typeof l) {
                    case "string":
                        return quote(l);
                    case "number":
                        return isFinite(l) ? String(l) : "null";
                    case "boolean":
                    case "null":
                        return String(l);
                    case "object":
                        if (!l) return "null";
                        if (gap += indent, r = [], "[object Array]" === Object.prototype.toString.apply(l)) {
                            for (i = l.length, n = 0; i > n; n += 1) r[n] = str(n, l) || "null";
                            return o = 0 === r.length ? "[]" : gap ? "[\n" + gap + r.join(",\n" + gap) + "\n" + s + "]" : "[" + r.join(",") + "]", gap = s, o
                        }
                        if (rep && "object" == typeof rep)
                            for (i = rep.length, n = 0; i > n; n += 1) a = rep[n], "string" == typeof a && (o = str(a, l), o && r.push(quote(a) + (gap ? ": " : ":") + o));
                        else
                            for (a in l) Object.hasOwnProperty.call(l, a) && (o = str(a, l), o && r.push(quote(a) + (gap ? ": " : ":") + o));
                        return o = 0 === r.length ? "{}" : gap ? "{\n" + gap + r.join(",\n" + gap) + "\n" + s + "}" : "{" + r.join(",") + "}", gap = s, o
                }
            }
            "function" != typeof Date.prototype.toJSON && (Date.prototype.toJSON = function (e) {
                return isFinite(this.valueOf()) ? this.getUTCFullYear() + "-" + f(this.getUTCMonth() + 1) + "-" + f(this.getUTCDate()) + "T" + f(this.getUTCHours()) + ":" + f(this.getUTCMinutes()) + ":" + f(this.getUTCSeconds()) + "Z" : null
            }, String.prototype.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON = function (e) {
                return this.valueOf()
            });
            var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
                escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
                gap, indent, meta = {
                    "\b": "\\b",
                    "	": "\\t",
                    "\n": "\\n",
                    "\f": "\\f",
                    "\r": "\\r",
                    '"': '\\"',
                    "\\": "\\\\"
                },
                rep;
            "function" != typeof AV.JSON.stringify && (AV.JSON.stringify = function (e, t, n) {
                var a;
                if (gap = "", indent = "", "number" == typeof n)
                    for (a = 0; n > a; a += 1) indent += " ";
                else "string" == typeof n && (indent = n);
                if (rep = t, t && "function" != typeof t && ("object" != typeof t || "number" != typeof t.length)) throw new Error("AV.JSON.stringify");
                return str("", {
                    "": e
                })
            }), "function" != typeof AV.JSON.parse && (AV.JSON.parse = function (text, reviver) {
                function walk(e, t) {
                    var n, a, o = e[t];
                    if (o && "object" == typeof o)
                        for (n in o) Object.hasOwnProperty.call(o, n) && (a = walk(o, n), void 0 !== a ? o[n] = a : delete o[n]);
                    return reviver.call(e, t, o)
                }
                var j;
                if (text = String(text), cx.lastIndex = 0, cx.test(text) && (text = text.replace(cx, function (e) {
                        return "\\u" + ("0000" + e.charCodeAt(0).toString(16)).slice(-4)
                })), /^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) return j = eval("(" + text + ")"), "function" == typeof reviver ? walk({
                    "": j
                }, "") : j;
                throw new SyntaxError("AV.JSON.parse")
            })
        }(), "undefined" == typeof AV && (AV = {}), AV.validLanguages = {
            en: !0,
            android: !0,
            bg: !0,
            zh_hans: !0,
            zh_hant: !0,
            cs: !0,
            da: !0,
            nl: !0,
            fi: !0,
            fr: !0,
            de: !0,
            el: !0,
            he: !0,
            hu: !0,
            id: !0,
            it: !0,
            ja: !0,
            ko: !0,
            lv: !0,
            lt: !0,
            no: !0,
            pl: !0,
            pt: !0,
            pt_br: !0,
            ru: !0,
            sk: !0,
            es: !0,
            sv: !0,
            tr: !0,
            vi: !0,
            uk: !0,
            ca: !0,
            ar: !0,
            sl: !0,
            th: !0
        }, AV.util = {
            getX: function (e) {
                for (var t = 0; null != e;) t += e.offsetLeft, e = e.offsetParent;
                return t
            },
            getY: function (e) {
                for (var t = 0; null != e;) t += e.offsetTop, e = e.offsetParent;
                return t
            },
            getTouch: function (e) {
                var t;
                return e.originalEvent && (e = e.originalEvent), t = e.changedTouches && 1 == e.changedTouches.length ? e.changedTouches[0] : e.touches && 1 == e.touches.length ? e.touches[0] : !1
            },
            getScaledDims: function (e, t, n, a) {
                a = a || n;
                var o = e,
                    i = t,
                    r = e / t;
                return (e > n || t > a) && (e - n > r * (t - a) ? (o = n, i = n * t / e + .5 | 0) : (o = a * r + .5 | 0, i = a)), {
                    width: o,
                    height: i
                }
            },
            nextFrame: function (e) {
                setTimeout(e, 1)
            },
            getDomain: function (e, t) {
                var n, a, o, i, r, s, l;
                return n = "http://" == e.substr(0, 7) ? 7 : "https://" == e.substr(0, 8) ? 8 : "ftp://" == e.substr(0, 6) ? 6 : 0, o = e.indexOf("/", n), -1 == o && (o = e.length), t ? a = n : (s = e, l = e.lastIndexOf(":"), s = l > n ? e.substring(n, l) : e.substring(n, o), s.match(/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/) ? a = n : (i = e.lastIndexOf(".", o), r = e.lastIndexOf(".", i - 1), a = -1 == r ? n : r + 1)), e.substring(a, o)
            },
            extend: function () {
                var e, t, n, a, o, i, r = arguments[0] || {},
                    s = 1,
                    l = arguments.length,
                    c = !1;
                for ("boolean" == typeof r && (c = r, r = arguments[1] || {}, s = 2), "object" == typeof r || jQuery.isFunction(r) || (r = {}), l === s && (r = this, --s) ; l > s; s++)
                    if (null != (e = arguments[s]))
                        for (t in e) n = r[t], a = e[t], r !== a && (c && a && (jQuery.isPlainObject(a) || (o = jQuery.isArray(a))) ? (o ? (o = !1, i = n && jQuery.isArray(n) ? n : []) : i = n && jQuery.isPlainObject(n) ? n : {}, r[t] = jQuery.extend(c, i, a)) : void 0 !== a && (r[t] = a));
                return r
            },
            findItemByKeyValueFromArray: function (e, t, n) {
                {
                    var a, o;
                    n.length
                }
                for (a = 0; a < n.length; a++)
                    if (n[a] && n[a][e] && n[a][e] === t) {
                        o = n[a];
                        break
                    }
                return o
            },
            loadFile: function () {
                var e, t, n, a, o = 0,
                    i = function (e, t) {
                        function n(e) {
                            (4 == this.readyState || "complete" == this.readyState || "loaded" == this.readyState) && t(e)
                        }
                        t && ("Microsoft Internet Explorer" == navigator.appName ? e.onreadystatechange = n : e.onload = t)
                    };
                return e = i,
                    function (i, r, s) {
                        var l;
                        return "js" == r ? (l = document.createElement("script"), l.setAttribute("type", "text/javascript"), e(l, s), l.setAttribute("src", i)) : "css" == r ? document.createStyleSheet ? document.createStyleSheet(i, o++) : (l = document.createElement("link"), l.setAttribute("rel", "stylesheet"), l.setAttribute("type", "text/css"), l.setAttribute("href", i)) : "img" == r && (l = document.createElement("img"), e(l, s), l.setAttribute("src", i)), l && (t = t || document.getElementsByTagName("head")[0], "js" == r ? t.appendChild(l) : "css" == r && (n = n || document.createDocumentFragment(), n.appendChild(l), t.insertBefore(l, a))), l
                    }
            }(),
            getImageElem: function (e) {
                return "string" == typeof e ? document.getElementById(e) : e.length ? e[0] : e
            },
            getImageId: function (e) {
                return "string" == typeof e ? e : e.id
            },
            imgOnLoad: function (e, t) {
                var n = avpw$(e);
                n.load(t), (1 == n[0].complete || 4 == n[0].readyState || "complete" == n[0].readyState || "loaded" == n[0].readyState) && n.trigger("load")
            },
            color_is_white: function (e) {
                return e = e.toLowerCase(), "#fff" == e || "#ffffff" == e || "white" == e || "rgb(255,255,255)" == e || "rgb(255, 255, 255)" == e
            },
            color_is_light: function (e) {
                var t, n, a, o, i;
                return t = n = a = 0, i = AV.util.color_to_array(e), t = i[0], n = i[1], a = i[2], o = .2 * t + .7 * n + .1 * a, o > 127.5
            },
            color_expand: function (e) {
                var t, n, a;
                return 4 == e.length && (t = e.charAt(1), n = e.charAt(2), a = e.charAt(3), e = "#" + t + t + n + n + a + a), e
            },
            color_to_array: function (e) {
                var t, n, a;
                return "#" == e.charAt(0) ? (e = AV.util.color_expand(e), t = parseInt(e.substr(1, 2), 16), n = parseInt(e.substr(3, 2), 16), a = parseInt(e.substr(5, 2), 16)) : "r" == e.charAt(0).toLowerCase() && (e = AV.util.rgb_to_color(e), t = parseInt(e.substr(1, 2), 16), n = parseInt(e.substr(3, 2), 16), a = parseInt(e.substr(5, 2), 16)), e = [t, n, a, 1]
            },
            array_to_color: function (e) {
                var t = AV.util.array_to_rgb(e);
                return t = AV.util.rgb_to_color(t)
            },
            array_to_rgb: function (e) {
                var t = "rgb(0,0,0)";
                return e.join && (e.length > 3 && (e = e.slice(0, 3)), t = "rgb(" + e.join(",") + ")"), t
            },
            color_to_rgb: function (e) {
                return e = AV.util.color_to_array(e), e = AV.util.array_to_rgb(e)
            },
            rgb_to_color: function (e) {
                var t, n, a, o = /\s*rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/,
                    i = e.match(o);
                return i ? (t = parseInt(i[1]).toString(16), 1 == t.length && (t = "0" + t), n = parseInt(i[2]).toString(16), 1 == n.length && (n = "0" + n), a = parseInt(i[3]).toString(16), 1 == a.length && (a = "0" + a), "#" + t + n + a) : e
            },
            color_to_int: function (e) {
                return e = AV.util.color_expand(e), e = AV.util.rgb_to_color(e), parseInt(e.substr(1), 16)
            },
            getSafeAssetBaseURL: function (e) {
                return e = e.replace("http://cd-test.aviary.com:1338", AV.build.cdsContentURL), "https:" == window.location.protocol && (e = AV.build.contentShouldUseStaging ? e.replace("http://testassets.aviary.com.s3.amazonaws.com", "https://s3.amazonaws.com/testassets.aviary.com") : e.replace("http://assets.aviary.com", "https://d2q6aqs27yssdp.cloudfront.net")), e
            },
            loadImagesSync: function (e, t, n) {
                var a = 0,
                    o = e.length,
                    i = function () {
                        t && a == e.length && AV.util.nextFrame(t)
                    },
                    r = avpw$.support.cors && !("Microsoft Internet Explorer" == navigator.appName) || n; -1 !== navigator.userAgent.indexOf("Safari") && -1 === navigator.userAgent.indexOf("Chrome") && (r = !1);
                for (var s = 0; o > s; s++) ! function (t) {
                    var n = e[t].img,
                        o = e[t].src;
                    n.onload = function () {
                        e[t].mappingObject && (e[t].mappingObject.w = n.width, e[t].mappingObject.h = n.height), a++, i()
                    }, r ? (n.crossOrigin = "Anonymous", n.src = o) : avpw$.ajax({
                        type: "GET",
                        dataType: "json",
                        url: AV.build.jsonp_imgserver + "?callback=?",
                        data: {
                            url: escape(o)
                        },
                        success: function (e) {
                            n.src = e.data
                        }
                    })
                }(s)
            },
            getApiVersion: function (e) {
                return e && e.apiVersion ? parseInt(e.apiVersion, 10) : 1
            },
            getUserFriendlyToolName: function (e) {
                var t = {
                    overlay: "Stickers",
                    drawing: "Draw",
                    textwithfont: "Text",
                    colorsplash: "Splash",
                    tiltshift: "Tilt Shift",
                    forcecrop: "Crop"
                },
                    n = "";
                return e && (n = t[e] || e.substr(0, 1).toUpperCase() + e.substr(1)), n
            },
            keyDownHandlerNumber: function (e, t) {
                9 == e.keyCode || 27 == e.keyCode || 65 == e.keyCode && (e.ctrlKey === !0 || e.metaKey === !0) || e.keyCode >= 35 && e.keyCode <= 39 || ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105) && 46 !== e.keyCode && 8 !== e.keyCode ? e.preventDefault() : t && t.apply(this, [e]))
            },
            getBrowserVersion: function () {
                var e, t = navigator.userAgent,
                    n = t.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
                return /trident/i.test(n[1]) ? (e = /\brv[ :]+(\d+)/g.exec(t) || [], "IE " + (e[1] || "")) : "Chrome" === n[1] && (e = t.match(/\bOPR\/(\d+)/), null != e) ? "Opera " + e[1] : (n = n[2] ? [n[1], n[2]] : [navigator.appName, navigator.appVersion, "-?"], null != (e = t.match(/version\/(\d+)/i)) && n.splice(1, 1, e[1]), n[1])
            },
            generatePointList: function (e, t) {
                var n, a = function (e, t, n, o, i, r, s, l) {
                    var c = (o - t) * (o - t) + (i - n) * (i - n);
                    if (l * l > c) return null;
                    var u, d, p, g, h, f;
                    void 0 !== r ? (u = (r + t) / 2, d = (r + o) / 2, p = (s + n) / 2, g = (s + i) / 2, h = (u + d) / 2, f = (p + g) / 2) : (h = (t + o) / 2, f = (n + i) / 2), a(e, t, n, h, f, u, p, l), e.push([0 | h, 0 | f]), a(e, h, f, o, i, d, g, l)
                },
                    o = Math.floor(.2 * t),
                    i = 0,
                    r = [];
                for (i = 0; i < e.length - 1; ++i) {
                    var s = e[i],
                        l = e[i + 1],
                        c = [(s[0] + l[0]) / 2 | 0, (s[1] + l[1]) / 2 | 0];
                    0 === i ? a(r, s[0], s[1], c[0], c[1], void 0, void 0, o) : a(r, n[0], n[1], c[0], c[1], s[0], s[1], void 0, void 0, o), n = c, r.push(c), i === e.length - 2 && (a(r, c[0], c[1], l[0], l[1], void 0, void 0, o), r.push(l))
                }
                return r
            },
            isURLSameDomain: function (e) {
                var t = window.location,
                    n = document.createElement("a");
                return n.href = e, n.hostname == t.hostname && n.port == t.port && n.protocol == t.protocol
            },
            doesSupportImageCORS: function () {
                return avpw$.support.cors && !("Microsoft Internet Explorer" == navigator.appName)
            },
            isIE11: function () {
                return !window.ActiveXObject && "ActiveXObject" in window || /Edge\/12./i.test(navigator.userAgent)
            }
        },
        function (e, t, n) {
            e.AV = e.AV || {};
            var a = e.AV;
            return a.ImageSizeTracker = function (e) {
                var t = this;
                t.setImageScaledIndicator = function () {
                    a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "updateImageScaledIndicator")
                }, t.setOrigSize = function (e, n, o) {
                    var i, r;
                    if (e.hiresWidth && e.hiresHeight) i = parseInt(e.hiresWidth, 10), r = parseInt(e.hiresHeight, 10);
                    else if (e.hiresUrl) i = n.width, r = n.height;
                    else {
                        if (!e.displayImageSize) return null;
                        i = o.width, r = o.height
                    }
                    return a.paintWidgetInstance.actions.setOrigSize(i, r), t.setImageScaledIndicator(), {
                        width: i,
                        height: r
                    }
                }, t.isDisplayingImageSize = function (e) {
                    return e.hiresWidth || e.hiresHeight || e.displayImageSize
                }, t.isUsingHiResDimensions = function (e) {
                    return e.hiresWidth || e.hiresHeight || e.displayImageSize && e.hiresUrl
                }
            }, e
        }(this, "undefined" != typeof window ? window : {}, "undefined" != typeof document ? document : {}),
        function (e, t, n) {
            e.AV = e.AV || {};
            var a = e.AV,
                o = a.Events;
            return a.ToolManager = function (e) {
                var i = null,
                    r = function (t) {
                        var n, o, i = e.activeTools,
                            r = !1;
                        if (i)
                            for (o = i.length, n = 0; o > n; n++)
                                if (i[n] === t) {
                                    r = !0;
                                    break
                                }
                        return "forcecrop" === t && a.launchData.forceCropPreset ? !0 : (r || a.errorNotify("UNSUPPORTED_TOOL", [t]), r)
                    },
                    s = function (t, n, a) {
                        return e.objectNotify("tool", t, n, a)
                    },
                    l = function (e) {
                        null != e && (avpw$(".avpw_controlpanel").each(function () {
                            avpw$(this).hide()
                        }), avpw$("#avpw_controlpanel_" + e).show())
                    },
                    c = function () {
                        var n, r = function (r) {
                            s(i, "panelWillClose"), s(r, "panelWillOpen"), o.trigger("canvas:activate", e.panelMode2WidgetMode(r)), l(r), s(r, "resetUI"), t.setTimeout(function (e) {
                                return function () {
                                    s(e, "panelDidClose"), i = r, s(r, "panelDidOpen"), n = !1
                                }
                            }(i), 200), i = r, e.layoutNotify(a.launchData.openType, "disableZoomMode")
                        };
                        return function (e) {
                            n || (n = !0, r(e))
                        }
                    }(),
                    u = function (t) {
                        if (!e.paintWidget || !e.paintWidget.busy) {
                            if (e.layoutNotify(a.launchData.openType, "showView", ["editpanel"]), c(t), a.controlsWidgetInstance.onEggWaitThrobber.stop(), "mobile" == a.launchData.openType) {
                                var o, i = n.getElementById("avpw_main_" + t);
                                i && (o = i.getAttribute("data-header"), o && (n.getElementById("avpw_control_toolname").innerHTML = o))
                            }
                            a.usageTracker.addUsage(t)
                        }
                    },
                    d = function () {
                        o.on("tool:open", h), o.on("tool:close", g), o.on("tool:init", f), o.on("tool:shutdown", y), o.on("tool:commit", v), o.on("tool:cancel", m), o.on("tool:undo", S), o.on("tool:redo", W)
                    },
                    p = function () {
                        o.off("tool:open", h), o.off("tool:close", g), o.off("tool:init", f), o.off("tool:shutdown", y), o.off("tool:commit", v), o.off("tool:cancel", m), o.off("tool:undo", S), o.off("tool:redo", W)
                    },
                    g = function () {
                        e.layoutNotify(a.launchData.openType, "showView", ["main"]), c(null)
                    },
                    h = function (t, n) {
                        t = a.publicName2PanelMode(t), (r(t) || a.launchData.forceCropPreset) && e.paintWidget && !e.paintWidget.moduleLoaded(t, u) && n && (e.onEggWaitThrobber.stop(), e.onEggWaitThrobber.spin(avpw$(n).children(".avpw_icon_waiter")[0])), o.trigger("usage:tool", t, "opened"), o.trigger("usage:firstclick", t)
                    },
                    f = function (t) {
                        s(t, "init", [e])
                    },
                    v = function () {
                        var e, t = i;
                        t && (e = s(i, "commit"), e !== !1 && (o.trigger("usage:tool", t, "applied", e !== !0 ? e : ""), o.trigger("tool:commitDone")))
                    },
                    m = function () {
                        s(i, "cancel"), o.trigger("usage:tool", i, "canceled"), a.featherGLEnabled && (e.paintWidget.moaGL.createEffect(), e.paintWidget.moaGL.renderIdentity(), e.paintWidget.moaGL.finalizeEffect(), e.paintWidget.moaGL.commit())
                    },
                    y = function (e) {
                        s(e, "shutdown")
                    },
                    w = function () {
                        return e.paintWidget.busy ? !1 : s(i, "onUndo") === !1 ? !1 : (e.paintWidget.actions.undo(), s(i, "onUndoComplete"), !1)
                    },
                    b = function () {
                        return e.paintWidget.busy ? !1 : s(i, "onUndo", [{
                            global: !0
                        }]) === !1 ? !1 : (e.paintWidget.actions.undoToCheckpoint(), s(i, "onUndoComplete", [{
                            global: !0
                        }]), !1)
                    },
                    _ = function () {
                        return e.paintWidget.busy ? !1 : s(i, "onRedo") === !1 ? !1 : (e.paintWidget.actions.redo(), s(i, "onRedoComplete"), !1)
                    },
                    I = function () {
                        if (e.paintWidget.busy) return !1;
                        if (s(i, "onRedo", [{
                            global: !0
                        }]) === !1) return !1;
                        var t = e.paintWidget.actions.redoToCheckpoint();
                        return t && s(i, "onRedoComplete", [{
                            global: !0
                        }]), t
                    },
                    S = function () {
                        o.trigger("usage:tool", "undo", "applied", i || ""), e.paintWidget.actions.isACheckpoint() ? b() : w()
                    },
                    W = function () {
                        o.trigger("usage:tool", "redo", "applied", i || ""), I() || _()
                    },
                    A = this;
                return A.init = d, A.shutdown = p, A.notify = s, A.getActiveTool = function () {
                    return i
                }, d(), A
            }, e
        }(this, "undefined" != typeof window ? window : {}, "undefined" != typeof document ? document : {}), AV.AssetManager = function (e, t) {
            "use strict";
            var n, a = {
                EFFECT: "effects",
                STICKER: "stickers",
                IMAGEBORDER: "frames",
                OVERLAYS: "overlays",
                PERMISSION: "permissions",
                FONTPACK: "fontpack"
            },
                o = {},
                i = function (e) {
                    return n[a[e]] || []
                },
                r = function (e, t) {
                    n ? t && t.apply(this, [i(e), a[e]]) : (o.getPartnerAssets || (o.getPartnerAssets = [], u.authenticate()), o.getPartnerAssets.push(function (o) {
                        o && o.status && "Ok" === o.status ? n = o : (AV.errorNotify("ERROR_AUTHENTICATING"), s()), AV.util.nextFrame(function () {
                            n && t && t.apply(this, [i(e), a[e]])
                        })
                    }))
                },
                s = function (e, t) {
                    return n = [{
                        needsPurchase: !1,
                        assetId: "default_effects",
                        assetType: "effect",
                        displayName: "Default",
                        resourceUrl: "js/proclist_default_effects.js"
                    }, {
                        needsPurchase: !1,
                        assetId: "original_effects",
                        assetType: "effect",
                        displayName: "Original",
                        resourceUrl: "js/proclist_original_effects.js"
                    }, {
                        needsPurchase: !1,
                        assetId: "original_stickers",
                        assetType: "sticker",
                        displayName: "Original",
                        resourceUrl: "js/stickers_original_stickers.js"
                    }, {
                        needsPurchase: !1,
                        assetId: "borders",
                        assetType: "imageborder",
                        displayName: "Default Image Borders",
                        resourceUrl: "js/borders_original.js"
                    }], t && AV.util.nextFrame(function () {
                        t.apply(this, [i(e)])
                    }), !0
                },
                l = function (e) {
                    var t, n, a;
                    if (e.messageName && (a = o[e.messageName])) {
                        if ("function" == typeof a) a.apply(this, [e.data]);
                        else
                            for (n = a.length, t = 0; n > t; t++) a[t].apply(this, [e.data]);
                        a = null
                    }
                },
                c = e ? r : s,
                u = this;
            return u.getAssets = c, u.getById = function (e) {
                for (var t = 0; t < n.length; t++)
                    if (n[t].assetId === e) return n[t]
            }, u.gatherContentAssetsHelper = function (e, t) {
                return u.getAssets(e, function (e) {
                    for (var n = {}, a = e, o = {}, i = function () {
                            s === a.length && t(o, a)
                    }, r = function (e, t) {
                            return AV.controlsWidgetInstance.serverMessaging.sendMessage({
                        id: "avpw_get_assetssticker",
                        action: t,
                        method: "GET",
                        dataType: "json",
                        announcer: AV.build.asyncFeatherTargetAnnounce,
                        origin: AV.build.asyncImgrecvBase,
                        callback: function (a) {
                                    o[e] = a, n[t] = !0, s++, i()
                    }
                    })
                    }, s = 0, l = 0; l < a.length; l++) {
                        var c = a[l].identifier,
                            u = AV.controlsWidgetInstance.assetManager.getContentURLByVersionKey(a[l].versionKey);
                        n[u] ? (s++, i()) : !o[c] && u && r(c, u)
                    }
                })
            }, u.getManifestURL = function () {
                var e, t, n = AV.build.contentShouldUseStaging;
                return AV.launchData.apiKey && AV.launchData.timestamp && AV.launchData.signature && AV.launchData.salt && AV.launchData.encryptionMethod ? (e = AV.build.gatewayAssetURL, t = ["&timestamp=", AV.launchData.timestamp, "&signature=", AV.launchData.signature, "&salt=", AV.launchData.salt, "&encryptionMethod=", AV.launchData.encryptionMethod].join("")) : e = AV.build.manifestURL, [e, "/hires/assets", "?platform=web", "&apiKey=", AV.launchData.apiKey || "", "&resolution=", window.devicePixelRatio > 1 ? "high" : "low", "&sdkVersion=" + AV.build.version, n ? "&staging=2" : "", t ? t : ""].join("")
            }, u.getContentURLByVersionKey = function (e) {
                var t = AV.build.contentShouldUseStaging;
                return AV.build.cdsContentURL + "/v1/content?versionKey=" + e + (t ? "&staging=2" : "")
            }, u.authenticate = function () {
                var e = function (e) {
                    var t = {
                        messageName: "getPartnerAssets",
                        data: e
                    };
                    l(t)
                };
                return function () {
                    return AV.controlsWidgetInstance.serverMessaging.sendMessage({
                        id: "avpw_auth_form",
                        action: u.getManifestURL(),
                        method: "GET",
                        dataType: "json",
                        announcer: AV.build.asyncFeatherTargetAnnounce,
                        origin: AV.build.asyncImgrecvBase,
                        callback: e,
                        onError: function (e) {
                            AV.errorNotify(e.status && 403 == e.status ? "ERROR_AUTHENTICATING" : "ERROR_GET_ASSETS")
                        }
                    })
                }
            }(), u.types = a, u
        }, AV.ServerMessaging = function (e) {
            var t = [],
                n = function (e, n) {
                    var a, o, i = !0,
                        r = t.shift();
                    if (r && (n && r.origin && (i = n === AV.util.getDomain(r.origin)), r.id && (a = avpw$("#" + r.id), o = a.attr("target"), avpw$("#" + o).unbind("load"), avpw$("#" + r.id + "_target_holder").empty(), a.remove()), i && r.callback)) {
                        if (r.dataType && "json" === r.dataType && "string" == typeof e) try {
                            e = AV.JSON.parse(e)
                        } catch (s) { }
                        r.callback.call(this, e)
                    }
                    t.length > 0 && d()
                },
                a = function (e, t, n, a, o, i) {
                    return avpw$.ajax({
                        url: e,
                        type: t,
                        data: n,
                        dataType: a,
                        error: function (t) {
                            i ? i.call(this, t) : AV.errorNotify("ERROR_SERVER_MESSAGING", [e])
                        },
                        success: o
                    })
                },
                o = function (e, t, n, a, o, i) {
                    var r = new XDomainRequest;
                    r.onload = function () {
                        var e = r.responseText;
                        window.setTimeout(function () {
                            var t;
                            try {
                                t = AV.JSON.parse(e), o(t)
                            } catch (n) {
                                o(e)
                            }
                        }, 0)
                    }, r.onerror = function (t) {
                        i ? i.call(this, t) : AV.errorNotify("ERROR_SERVER_MESSAGING", [e]), AV.errorNotify("ERROR_SERVER_MESSAGING", [e])
                    }, r.ontimeout = function () { }, r.onprogress = function () { }, r.open(t, e), n ? r.send(avpw$.param(n)) : r.send()
                },
                i = function (e, t, n, a, o) {
                    var i = avpw$("<form></form>").attr({
                        id: e,
                        action: t,
                        target: n,
                        method: a || "POST"
                    }).css({
                        display: "none"
                    }),
                        r = document.createDocumentFragment();
                    for (var s in o) o.hasOwnProperty(s) && r.appendChild(avpw$("<input></input>").attr({
                        name: s,
                        value: o[s],
                        type: "hidden"
                    })[0]);
                    return i.html(r), i.appendTo("#avpw_holder"), i
                },
                r = function (e, t, n) {
                    return n || (n = AV.build.feather_baseURL + "blank.html"), t || (t = e), ['<iframe width="1" height="1" ', 'style="position:absolute;left:-9999px;" ', 'id="' + e + '" name="' + t + '" src="' + n + '">', "</iframe>"].join("")
                },
                s = function (e, t, n) {
                    if (!e) return null;
                    var a = e + "_target_holder",
                        o = Math.floor(4294967295 * Math.random()).toString(16),
                        i = "avpw_form_target_" + o,
                        s = avpw$("#" + a);
                    return s && s.length || (s = avpw$('<div id="' + a + '"></div>').css({
                        position: "absolute",
                        top: 0,
                        left: 0
                    }).appendTo("#avpw_holder")), s.html(r(i)), avpw$("#" + i).load(t ? function () {
                        c(i, e, t)
                    } : n), i
                },
                l = function (e, t, n, a, o, r, l) {
                    var c = s(e, r, l);
                    t += "?responsecontenttypeheader=" + escape("text/html");
                    var u = i(e, t, c, n, o);
                    return u.submit(), u
                },
                c = function (e, t, a) {
                    var o, i = t + "_announcer";
                    if (window.postMessage) window[i] ? window[i].postMessage("avpw_load:" + e, "*") : (o = avpw$(r(i, i, a)), o.load(function () {
                        AV.util.nextFrame(function () {
                            window[i].postMessage("avpw_load:" + e, "*")
                        })
                    }), avpw$("#avpw_holder").append(o));
                    else {
                        var s, l = function () {
                            avpw$(s).unbind().remove()
                        },
                            c = t + "_observer",
                            u = c,
                            d = 0,
                            p = function () {
                                var e;
                                try {
                                    if ("about:blank" == s.contentWindow.location) return
                                } catch (t) { }
                                2 === d && (e = s.contentWindow.name, e && (d = 3, e !== u && e.substr && "avpw:" == e.substr(0, 5) ? (e = e.substr(5), n(e)) : (AV.errorNotify("ERROR_SAVING", [AV.build.imgrecvServer]), n()), l())), 1 === d && (d = 2, s.contentWindow.location = ""), d || (d = 1)
                            };
                        s = avpw$(r(c, u, a + "#" + e))[0], avpw$(s).load(p), avpw$(s).appendTo("#avpw_holder")
                    }
                },
                u = function (e) {
                    var t = e.data,
                        a = AV.util.getDomain(e.origin);
                    t.substr && "avpw:" == t.substr(0, 5) && (t = t.substr(5), n(t, a))
                },
                d = function () {
                    var e = t[0];
                    e && l(e.id, e.action, e.method, e.origin, e.keyValues, e.announcer)
                },
                p = function (e) {
                    e.announcer ? (t.push(e), 1 === t.length && d()) : l(e.id, e.action, e.method, e.origin, e.keyValues, e.announcer, e.callback)
                },
                g = function (e) {
                    var t, n = e.transport || "xhr";
                    "xhr" === n && avpw$.support.cors && (!AV.firefox || AV.firefox >= 4) ? (t = a(e.action, e.method, e.keyValues, e.dataType, e.callback, e.onError), t || p(e)) : "function" == typeof XDomainRequest ? o(e.action, e.method, e.keyValues, e.dataType, e.callback, e.onError) : p(e)
                },
                h = function () {
                    window.addEventListener ? window.addEventListener("message", u, !1) : window.attachEvent && window.attachEvent("onmessage", u)
                },
                f = this;
            return f.shutdown = function () {
                window.removeEventListener ? window.removeEventListener("message", u, !1) : window.detachEvent && window.detachEvent("onmessage", u), t = []
            }, f.sendMessage = g, h(), f
        },
        function (e, t, n) {
            e.AV = e.AV || {};
            var a = e.AV,
                o = a.Events;
            a.usageTracker = function () {
                var e, i = null,
                    r = {},
                    s = 0,
                    l = [],
                    c = 0,
                    u = -1,
                    d = !1,
                    p = {},
                    g = function () {
                        a.controlsWidgetInstance && p.submit("close")
                    },
                    h = function () {
                        d || (! function (e, t, n, a, o, i, r) {
                            e.GoogleAnalyticsObject = o, e[o] = e[o] || function () {
                                (e[o].q = e[o].q || []).push(arguments)
                            }, e[o].l = 1 * new Date, i = t.createElement(n), r = t.getElementsByTagName(n)[0], i.async = 1, i.src = a, r.parentNode.insertBefore(i, r)
                        }(t, n, "script", "https://www.google-analytics.com/analytics.js", "AV_ga"), AV_ga("create", a.build.googleTracker, "auto", {
                            allowLinker: !0
                        }), AV_ga("set", "dimension1", a.launchData.apiKey), AV_ga("set", "dimension2", a.build.version), AV_ga("set", "dimension3", this.getUUID()), AV_ga("set", "dimension4", a.launchData.language), AV_ga("set", "dimension5", a.launchData.apiVersion + ""), d = !0, AV_ga("send", "event", "editor", "isWebGLUsed", a.featherGLEnabled.toString()))
                    },
                    f = function (e, t, n) {
                        AV_ga("send", "event", "tool", e + ":" + t, n ? n + "" : "")
                    },
                    v = function (e, t, n) {
                        AV_ga("send", "event", "interaction", e + ":" + t, n ? n + "" : "")
                    },
                    m = function (e) {
                        p.submit("firstclick", e), o.off("usage:firstclick")
                    };
                return p.setup = function () {
                    avpw$(t).bind("unload", g), o.on("usage:submit", p.submit, p), o.on("usage:tool", f, p), o.on("usage:firstclick", m, p), o.on("usage:interact", v)
                }, p.shutdown = function () {
                    avpw$(t).unbind("unload", g), o.off("usage:submit", p.submit), o.off("usage:tool", f), o.off("usage:firstclick", m), o.off("usage:interact", v)
                }, p.clear = function () {
                    i = null, r = {}, s = 0, l = [], c = 0, u = -1
                }, p.getUUID = function () {
                    return i ? i : i = Math.floor(4294967295 * Math.random()).toString(16) + Math.floor(4294967295 * Math.random()).toString(16)
                }, p.addUsage = function (e, t) {
                    t || (t = 1), void 0 === r[e] ? r[e] = t : r[e] += t, s += t
                }, p.setPageCount = function (e) {
                    var t;
                    for (c = e, l = new Array(e), t = 0; e > t; t++) l[t] = 0
                }, p.addPageHit = function (t) {
                    t !== e && l[t]++, e = t
                }, p.submit = function (e, n) {
                    h.call(this), "launch" === e ? AV_ga("send", "pageview", (t.location || "").toString()) : AV_ga("send", "event", "submit", e, n)
                }, p
            }();
            var i = {
                lighting: {
                    brightness: !0,
                    contrast: !0
                },
                color: {
                    saturation: !0,
                    warmth: !0
                }
            };
            return a.getActiveTools = function (e) {
                var t = a.featherUseFlash ? a.flashSupportedTools : a.featherGLEnabled ? a.glSupportedTools : a.defaultTools,
                    n = e;
                n && "all" !== n && "All" !== n && "ALL" !== n && "" !== n || (n = t), "string" == typeof n && (n = e.split(","));
                var o, r, s = [],
                    l = {},
                    c = {};
                for (r = 0; r < t.length; r++) o = t[r], c[o] = !0;
                var u = !0;
                for (r = 0; r < n.length; r++) {
                    if (a.launchData.forceCropPreset) {
                        if ("resize" === n[r] || "orientation" === n[r] || "crop" === n[r] || "overlays" === n[r]) continue
                    } else if ("orientation" === n[r] && avpw$.browser.msie && 9 === parseInt(avpw$.browser.version)) continue;
                    o = a.publicName2PanelMode(n[r]), o in c ? (s.push(o), u = !1, l[o] = !0) : !l.lighting && i.lighting[o] ? (s.push("lighting"), l.lighting = !0, u = !1) : !l.color && i.color[o] && (s.push("color"), l.color = !0, u = !1)
                }
                return a.launchData.forceCropPreset && u && a.errorNotify("BAD_FORCECROP_TOOLS"), s
            }, a.paintWidgetGetPopupEmbedDiv = function (e) {
                var t = avpw$("#avpw_canvas_embed_popup");
                if (e) {
                    var a, o, i, r = avpw$(e),
                        s = ["top", "left", "bottom", "right", "margin-top", "margin-right", "margin-bottom", "margin-left", "border-top", "border-right", "border-bottom", "border-left", "padding-top", "padding-right", "padding-bottom", "padding-left"],
                        l = {
                            position: "relative"
                        };
                    for (o = 0; o < s.length; o++) i = s[o], l[i] = r.css(i);
                    a = avpw$(e).css("display"), ("" == a || "inline" == a) && (a = "inline-block"), l.display = a, 0 == t.length && (t = n.createElement("div"), t.id = "avpw_canvas_embed_popup"), avpw$(t).hide().css(l).insertBefore(e)
                }
                return t
            }, a.paintWidgetLauncher = function (e, t) {
                return a.paintWidgetInstance ? void 0 : (a.usageTracker.clear(), a.paintWidgetLauncher_HTML(e, t))
            }, a.paintWidgetLauncher_HTML = function (e, t) {
                var o, i, r, s, l, c = a.launchData.launchDelay,
                    u = a.util.getImageElem(e);
                return s = a.getActiveTools(a.launchData.tools), a.isRelaunched && "undefined" != typeof u.avpw_prevURL && (t = u.avpw_prevURL), l = new a.AssetManager(a.launchData.isPremiumPartner, a.launchData.allowInAppPurchase), a.controlsWidgetInstance = new a.ControlsWidget(null, e, s, l, new a.ServerMessaging), a.launchData.image instanceof HTMLImageElement && !t && (t = e.src), t && 0 === t.indexOf("//") && (t = n.location.protocol + t), a.controlsWidgetInstance.origURL = t ? t : avpw$(u).attr("src"), a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "placeControls", [a.util.getApiVersion(a.launchData) > 1 ? a.launchData.appendTo : void 0]), a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "enableControls"), a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "bindEvents"), avpw$("#avpw_controls").fadeIn(c), a.util.nextFrame(function () {
                    "mobile" == a.launchData.openType && a.setPageWidth(avpw$("#avpw_controls").width()), a.controlsWidgetInstance.setupScrollPanels()
                }), a.launchData.noCloseButton && avpw$("#avpw_control_cancel_pane").css("display", "none"), u && "canvas" === u.nodeName.toLowerCase() ? void a.mockLauncher(u) : (i = a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "getEmbedElement", [u]), o = n.createElement("img"), o.id = "avpw_temp_loading_image", a.tempLoadingImageSrc = o.src, avpw$(o).load(function () {
                    r = a.controlsWidgetInstance.getScaledDims(avpw$(u).width(), avpw$(u).height()), o.width = r.width, o.height = r.height, a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "getScaledImageDims", [o]), avpw$(o).unbind(), o.style.display = "block", avpw$(i).append(o), a.controlsWidgetInstance.showWaitThrobber(!0), a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "hideOriginalImage", [u]), avpw$(i).show(), a.util.nextFrame(function () {
                        a.paintWidgetLauncher_stage2(e, t)
                    })
                }).error(function () {
                    a.paintWidgetCloser(!0), a.errorNotify("BAD_IMAGE", [t])
                }), o.src = u.src, !1)
            }, a.paintWidgetLauncher_stage2 = function (e, t) {
                var n, i, r = a.util.getImageElem(e),
                    s = function (e) {
                        a.controlsWidgetInstance && a.paintWidgetInstance && (n = new Image, avpw$.support.cors && a.launchData.enableCORS && -1 === e.indexOf("data:") && (n.crossOrigin = "Anonymous"), avpw$(n).load(function (e) {
                            if (a.controlsWidgetInstance && a.paintWidgetInstance) {
                                if (i = a.controlsWidgetInstance.getScaledDims(n.width, n.height), a.controlsWidgetInstance.imageSizeTracker.setOrigSize(a.launchData, n, i), n.width = i.width, n.height = i.height, a.paintWidgetInstance.setDimensions(i.width, i.height), !a.paintWidgetInstance.setBackground(n)) return a.paintWidgetCloser(!0), a.errorNotify("IMAGE_NOT_CLEAN", [t]), !1;
                                a.paintWidgetInstance.setOrigSize(i.width, i.height), r.src !== t && a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "scaleCanvas"), avpw$(a.paintWidgetInstance.canvas).insertBefore("#avpw_temp_loading_image"), l.remove(), a.tempLoadingImageSrc = t, a.controlsWidgetInstance.showWaitThrobber(!1), a.controlsWidgetInstance.loaderPhase = 2, a.launchData.actionListJSON && a.paintWidgetInstance.actions.importJSON(a.launchData.actionListJSON, a.fireLaunchComplete)
                            }
                        }).attr("src", e))
                    };
                i = a.controlsWidgetInstance.getScaledDims(avpw$(r).width(), avpw$(r).height()), a.controlsWidgetInstance.loaderPhase = 1, a.paintWidgetInstance = new a.PaintWidget(i.width, i.height, new a.Actions, new a.ModeManager, new a.FilterManager, new a.OverlayRegistry, new a.ImageBorderManager), a.controlsWidgetInstance.canvasUI = new a.PaintUI(a.paintWidgetInstance.canvas, a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "getEmbedElement")), a.controlsWidgetInstance.initWithPaintWidget(a.paintWidgetInstance), a.paintWidgetInstance.setOrigSize(i.width, i.height), a.controlsWidgetInstance.imageSizeTracker.setOrigSize(a.launchData, r, i);
                var l = avpw$("#avpw_temp_loading_image");
                if (a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "scaleCanvas"), null != t)
                    if (!a.launchData.enableCORS || !avpw$.support.cors || avpw$.browser.msie || avpw$.browser.safari && -1 === navigator.userAgent.indexOf("Chrome") && parseInt(a.util.getBrowserVersion()) < 7)
                        if (-1 === t.indexOf("data:")) {
                            if (!t || !t.match(/^http(s|):\/\//)) return void a.errorNotify("BAD_URL", [t]);
                            a.util.isURLSameDomain(t) ? s(t) : avpw$.ajax({
                                type: "GET",
                                dataType: "json",
                                url: a.build.jsonp_imgserver + "?callback=?",
                                data: {
                                    url: escape(t)
                                },
                                success: function (e) {
                                    s(e.data)
                                },
                                error: function (e, n, o) {
                                    200 === e.status && "parsererror" === n ? a.controlsWidgetInstance && (a.controlsWidgetInstance.showWaitThrobber(!1), a.util.nextFrame(function () {
                                        a.paintWidgetCloser(!0), a.errorNotify("BAD_URL", [t])
                                    })) : a.controlsWidgetInstance && (a.controlsWidgetInstance.showWaitThrobber(!1), a.paintWidgetCloser(!0), a.errorNotify("ERROR_SERVER_MESSAGING", [t]))
                                }
                            })
                        } else s(t);
                    else s(t);
                else {
                    if (!a.paintWidgetInstance.setBackground(r)) return a.paintWidgetCloser(!0), a.launchData.enableCORS && avpw$.support.cors ? a.errorNotify("ERROR_BAD_IMAGE_WITHOUT_CORS") : a.errorNotify("IMAGE_NOT_CLEAN", [t]), !1;
                    avpw$("#avpw_controls").insertAfter(a.paintWidgetInstance.canvas), avpw$(a.paintWidgetInstance.canvas).insertBefore(l), l.remove(), a.tempLoadingImageSrc = r.src, a.controlsWidgetInstance.showWaitThrobber(!1), a.controlsWidgetInstance.loaderPhase = 2, a.launchData.actionListJSON && a.paintWidgetInstance.actions.importJSON(a.launchData.actionListJSON, a.fireLaunchComplete)
                }
                return o.trigger("usage:submit", "launch"), a.launchData.actionListJSON || a.fireLaunchComplete(), !1
            }, a.fireLaunchComplete = function () {
                var e = a.launchData.initTool;
                a.Events.trigger("layout:resize"), e && (a.util.nextFrame(function () {
                    o.trigger("tool:open", e)
                }), a.paintWidgetInstance.moduleLoaded(e, function (e) {
                    a.util.nextFrame(function () {
                        avpw$("#avpw_holder").removeClass("avpw_init_hide")
                    })
                })), "function" == typeof a.launchData.onReady && a.launchData.onReady()
            }, a.paintWidgetShutdown = function () {
                o.trigger("usage:submit", "close"), a.paintWidgetInstance && a.paintWidgetInstance.shutdown(), a.controlsWidgetInstance && (a.controlsWidgetInstance.serverMessaging && (a.controlsWidgetInstance.serverMessaging.shutdown(), a.controlsWidgetInstance.serverMessaging = null), a.controlsWidgetInstance.shutdown()), avpw$("#avpw_controls").hide(), a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "onShutdown"), "function" == typeof a.launchData.onClose && a.launchData.onClose(a.paintWidgetInstance.dirty), a.paintWidgetInstance = null, a.controlsWidgetInstance = null, a.tempLoadingImageSrc = null
            }, a.paintWidgetCloser = function (e) {
                var t = a.launchData.closeDelay;
                a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "onClose", [e]), e || 0 === t ? (avpw$("#avpw_controls").hide(), a.paintWidgetShutdown()) : avpw$("#avpw_controls").fadeOut(t, function () {
                    a.paintWidgetInstance && a.paintWidgetShutdown()
                })
            }, a.controlsWidget_saveResponder = function (e, t, i) {
                "https:" === n.location.protocol && ("string" == typeof t && (t = t.replace("http:", "https:")), "string" == typeof i && (i = i.replace("http:", "https:")));
                var r;
                if ("function" == typeof e && (r = e.apply(a.launchData, [a.util.getImageId(a.controlsWidgetInstance.paintImgIdElem), t, i])), a.controlsWidgetInstance) {
                    var s = a.util.getImageElem(a.controlsWidgetInstance.paintImgIdElem);
                    s.avpw_prevURL = t, o.trigger("tool:close"), r && a.controlsWidgetInstance.messager.show("avpw_aviary_beensaved", !0), a.controlsWidgetInstance.paintWidget.dirty = !1, a.controlsWidgetInstance.saving = !1
                }
            }, a.controlsWidget_onImageSaved = function (e, t) {
                a.controlsWidget_saveResponder(a.launchData.onSave, e, t)
            }, a.controlsWidget_onHiResImageSaved = function (e) {
                a.controlsWidget_saveResponder(a.launchData.onSaveHiRes, e)
            }, a.ControlsWidget = function (e, t, n, i, r) {
                this.maxWidth = parseInt(a.launchData.maxSize), this.maxHeight = this.maxWidth, this.saving = !1, this.origURL = null, this.activeTools = n, this.quitCount = 0, a.usageTracker.setup(), this.paintImgIdElem = t, o.on("layout:resize", this.setupScrollPanels, this), this.layoutNotify(a.launchData.openType, "showView", ["main"]), e && this.initWithPaintWidget(e);
                var s = {
                    className: "avpw_canvas_spinner",
                    lines: 12,
                    length: 6,
                    width: 2,
                    radius: 6,
                    color: "#fff",
                    speed: .5,
                    trail: 70
                },
                    l = {
                        className: "avpw_tool_spinner",
                        lines: 12,
                        length: 6,
                        width: 2,
                        radius: 6,
                        color: "#fff",
                        speed: .5,
                        trail: 70
                    };
                "mobile" != a.launchData.openType && (l.color = "#555", l.length = 4), this.waitThrobber = new a.Spinner(s), this.onEggWaitThrobber = new a.Spinner(l), this.toolManager = new a.ToolManager(this), this.assetManager = i, this.serverMessaging = r
            }, a.ControlsWidget.prototype.tool = {}, a.ControlsWidget.prototype.layout = {}, a.ControlsWidget.prototype.layoutNotify = function (e, t, n) {
                return this.objectNotify("layout", e, t, n)
            }, a.ControlsWidget.prototype.objectNotify = function (e, t, n, a) {
                if ("object" == typeof this[e][t]) {
                    var o = this[e][t];
                    if ("function" == typeof o[n]) {
                        var i = o[n];
                        return a || (a = []), i.apply(o, a)
                    }
                }
                return !0
            }, a.ControlsWidget.prototype.shutdown = function () {
                this.canvasUI && this.canvasUI.shutdown(), this.messager && this.messager.hide(), o.off("layout:resize", this.setupScrollPanels), this.shutdownAllTools(), this.unbindControls(), this.toolsPager && (this.toolsPager.shutdown(), this.toolsPager = null), this.paintWidget && (this.paintWidget.showWaitThrobber = null), a.usageTracker.shutdown(), this.waitThrobber.stop(), this.onEggWaitThrobber.stop(), this.waitThrobber = null, this.onEggWaitThrobber = null, this.showPanel(null), this.toolManager.shutdown(), this.toolManager = null
            }, a.ControlsWidget.prototype.initAllTools = function () {
                for (var e in this.activeTools) {
                    var t = this.activeTools[e];
                    this.tool.hasOwnProperty(t) && a.paintWidgetInstance.moduleLoaded(t, function (e) {
                        o.trigger("tool:init", e)
                    }.AV_bindInst(this))
                }
                a.launchData.forceCropPreset && a.paintWidgetInstance.moduleLoaded("forcecrop", function (e) {
                    o.trigger("tool:init", "forcecrop")
                }.AV_bindInst(this))
            }, a.ControlsWidget.prototype.shutdownAllTools = function () {
                for (var e in this.activeTools) {
                    var t = this.activeTools[e];
                    o.trigger("tool:shutdown", t)
                }
                a.launchData && a.launchData.forceCropPreset && o.trigger("tool:shutdown", "forcecrop")
            }, a.ControlsWidget.prototype.bindControls = function () { }, a.ControlsWidget.prototype.unbindControls = function () { }, a.ControlsWidget.prototype.initWithPaintWidget = function (e) {
                this.paintWidget = e, this.imageSizeTracker = new a.ImageSizeTracker(e.actions), a.featherUseFlash || this.initAllTools(), this.bindControls(), this.paintWidget.showWaitThrobber = this.showWaitThrobber.AV_bindInst(this)
            }, a.ControlsWidget.prototype.showWaitThrobber = function (e, n) {
                var o = 300,
                    i = this;
                if (e) {
                    var r = this.layoutNotify(a.launchData.openType, "getEmbedElement");
                    r.is(":visible") && (this.waitThrobber.spin(r[0]), avpw$(this.waitThrobber).fadeIn(o))
                } else avpw$(i.waitThrobber.el).fadeOut(o, i.waitThrobber.stop);
                n && t.setTimeout(n, 5)
            }, a.publicName2PanelMode = function (e) {
                return "stickers" === e && (e = "overlay"), "draw" === e && (e = "drawing"), "text" !== e || a.featherUseFlash || (e = "textwithfont"), e
            }, a.ControlsWidget.prototype.panelMode2WidgetMode = function (e) {
                switch (e) {
                    case "rotate":
                        return "rotate90";
                    case "greeneye":
                        return "redeye";
                    case "pinch":
                        return "bulge"
                }
                return e
            }, a.ControlsWidget.prototype.setupScrollPanels = function () {
                if (this.activeTools && this.activeTools.length) {
                    var toolcount = 3;
                    if (this.layoutNotify(a.launchData.openType, "getDims").TOOLS_BROWSER_WIDTH > 500 && this.layoutNotify(a.launchData.openType, "getDims").TOOLS_BROWSER_WIDTH < 768) {
                        toolcount = 5;
                    }
                    else if (this.layoutNotify(a.launchData.openType, "getDims").TOOLS_BROWSER_WIDTH > 768) {
                        toolcount = this.layoutNotify(a.launchData.openType, "getToolsPerPage");
                    }
                    
                    var e, t, n, o = this,
                       i = {},
                       r = this.layoutNotify(a.launchData.openType, "getDims").TOOLS_BROWSER_WIDTH,
                       s = function () {
                           o.toolsPager = new a.Pager(l), o.toolsPager.changePage()
                       },
                       l = {
                           itemCount: this.activeTools.length,
                           // itemsPerPage: this.layoutNotify(a.launchData.openType, "getToolsPerPage"), // for only 3 tools per page--jigar 

                           itemsPerPage: toolcount,
                           pageWidth: r,
                           leftArrow: avpw$("#avpw_lftArrow"),
                           rightArrow: avpw$("#avpw_rghtArrow"),
                           itemBuilder: function (n) {
                               return e = o.activeTools[n], t = a.util.getUserFriendlyToolName(e), t = a.getLocalizedString(t), a.template[a.launchData.layout].eggIcon({
                                   optionName: e,
                                   capOptionName: t
                               })
                           },
                           pageTemplate: a.template[a.launchData.layout].genericScrollPanel,
                           pipTemplate: a.template[a.launchData.layout].scrollPanelPip,
                           lastPageTemplate: n,
                           lastPageContents: i,
                           pageContainer: avpw$("#avpw_control_main_scrolling_region"),
                           pipContainer: avpw$("#avpw_tools_pager ul"),
                           onPageChange: function (e) {
                               a.usageTracker.addPageHit(e)
                           }
                       };
                    s(), avpw$("#avpw_control_main_scrolling_region").css("width", o.toolsPager.getPageCount() * r + "px"), this.assetManager.getAssets("PERMISSION", function (e) {
                        var t = !0;
                        if (e)
                            for (var n = 0; n < e.length; n++)
                                if ("whitelabel" === e[n]) {
                                    t = !1;
                                    break
                                }
                        t ? (avpw$("#avpw_powered_branding").html(a.template[a.launchData.layout].poweredByFooterLogo).find("a").css("cursor", "default"), o.toolsPager.shutdown(), s(), avpw$("#avpw_control_main_scrolling_region").css("width", o.toolsPager.getPageCount() * r + "px"), o.toolsPager.changePage()) : avpw$("#avpw_controls").addClass("avpw_white_label")
                    }), a.usageTracker.setPageCount(o.toolsPager.getPageCount())
                }
            }, a.ControlsWidget.prototype.messager = function () {
                var e, n, i, r = {},
                    s = 1e3,
                    l = {
                        show: function (o, i, l) {
                            e = e || avpw$("#avpw_messaging"), n = n || avpw$("#avpw_messaging_inner");
                            var c = r[o] || (r[o] = avpw$("#" + o));
                            n.append(c), e.fadeIn(150), i ? (e.data("needsConfirmation", !0), a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "disableControls")) : (e.data("needsConfirmation", !1), l || t.setTimeout(this.hide, s))
                        },
                        hide: function (n, o) {
                            if (e = e || avpw$("#avpw_messaging"), i = i || avpw$("#avpw_messages"), n) {
                                var s = r[n];
                                s && i.append(s)
                            } else avpw$.each(r, function (e, t) {
                                i.append(t)
                            });
                            e.data("needsConfirmation") ? (t.setTimeout(function () {
                                o && o()
                            }, 400), a.controlsWidgetInstance && a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "enableControls")) : (e.hide(), o && o())
                        },
                        addMessage: function (e) {
                            i = i || avpw$("#avpw_messages"), e && (i[0].innerHTML += e)
                        }
                    };
                return o.on("modal:show", l.show), o.on("modal:hide", l.hide), l
            }(), a.ControlsWidget.prototype.orientationChanged = function (e) { }, a.ControlsWidget.prototype.windowResized = function () {
                var e = null;
                return function (n) {
                    t.clearTimeout(e), e = t.setTimeout(function () {
                        o.trigger("layout:resize"), e = null
                    }, 500)
                }
            }(), a.ControlsWidget.prototype.Slider = function (e) {
                var t = !1,
                    n = function (n, a) {
                        !t && e.onstart && e.onstart.apply(this, [n, a])
                    },
                    a = function (n, a) {
                        !t && e.onchange && e.onchange.apply(this, [n, a])
                    },
                    o = function (n, a) {
                        !t && e.onslide && e.onslide.apply(this, [n, a])
                    },
                    i = avpw$(e.element).slider({
                        range: "min",
                        max: e.max,
                        min: e.min,
                        value: e.defaultValue || e.max / 2,
                        delay: e.delay
                    });
                return this.getValue = function () {
                    return i.slider("value")
                }, this.setValue = function (e) {
                    return i.slider("value", e)
                }, this.reset = function (n) {
                    t = !0, this.setValue(e.defaultValue), t = !1
                }, this.addListeners = function () {
                    i.bind("slidestart", n).bind("slidechange", a).bind("slide", o)
                }, this.removeListeners = function () {
                    i.unbind("slidestart").unbind("slide").unbind("slidechange")
                }, this.shutdown = function () {
                    i.slider("destroy")
                }, this
            }, a.ControlsWidget.prototype._drawUICircle = function (e, t, n, o, i) {
                a.featherUseFlash || this._drawUICircle_HTML(e, t, n, o, i)
            }, a.ControlsWidget.prototype._drawUICircle_HTML = function (e, t, n, o, i) {
                var r, s = avpw$(e)[0],
                    l = s.getContext("2d");
                l.clearRect(0, 0, s.width, s.height), i && "transparent" !== o && this._drawUIGrid(l, s.width, s.height), l.globalCompositeOperation = "source-over", null != n ? (l.strokeStyle = i && ("transparent" == n || a.util.color_is_white(n) || null == o) ? "#444" : n, r = 3) : (l.strokeStyle = "rgba(0,0,0,0)", r = 1), l.lineWidth = r, l.beginPath(), l.arc(s.width / 2, s.height / 2, t, 0, 2 * Math.PI, !0), l.stroke(), l.closePath(), null != o && (l.save(), l.clip(), i && "transparent" == o ? this._drawUIGrid(l, s.width, s.height) : (l.fillStyle = o, l.fillRect(0, 0, s.width, s.height)), l.restore())
            }, a.ControlsWidget.prototype._drawUIGrid = function (e, t, n, a) {
                var o, i;
                for (a || (a = 5), i = 0; n + a > i; i += a)
                    for (o = 0; n + a > o; o += a) e.fillStyle = 1 == (o + i & 1) ? "#fff" : "#ddd", e.fillRect(o, i, a, a)
            }, a.ControlsWidget.prototype.showPanel = function (e) {
                null != e && (avpw$(".avpw_controlpanel").each(function () {
                    avpw$(this).hide()
                }), avpw$("#avpw_controlpanel_" + e).show())
            }, a.ControlsWidget.prototype.save = function () {
                function e(e, t) {
                    return e = e.toLowerCase(), -1 !== e.indexOf("jpg", e.length - "jpg".length) ? "image/jpg" : -1 !== e.indexOf("jpeg", e.length - "jpeg".length) ? "image/jpeg" : -1 !== e.indexOf("gif", e.length - "gif".length) ? "image/gif" : -1 !== e.indexOf("tiff", e.length - "tiff".length) ? "image/tiff" : -1 !== e.indexOf("bmp", e.length - "bmp".length) ? "image/bmp" : -1 !== e.indexOf("png", e.length - "png".length) ? "image/png" : t
                }
                var n = null,
                    i = !0,
                    r = 1e3,
                    s = function () {
                        return {
                            apikey: a.launchData.apiKey,
                            timestamp: a.launchData.timestamp,
                            signature: a.launchData.signature,
                            salt: a.launchData.salt,
                            encryptionMethod: a.launchData.encryptionMethod
                        }
                    },
                    l = function (e, t, n) {
                        var o = a.controlsWidgetInstance;
                        if (o.layoutNotify(a.launchData.openType, "enableControls", [!0]), o.paintWidget.showWaitThrobber(!1), e) {
                            var i = avpw$(a.util.getImageElem(o.paintImgIdElem));
                            i.avpw_prevURL = e, a.controlsWidget_onImageSaved(e, t)
                        } else a.errorNotify("ERROR_SAVING", [a.build.imgrecvServer, n]), a.controlsWidgetInstance.saving = !1
                    },
                    c = function (e, t) {
                        var n = a.controlsWidgetInstance;
                        if (n.layoutNotify(a.launchData.openType, "enableControls", [!0]), n.paintWidget.showWaitThrobber(!1), e) {
                            var o = avpw$(a.util.getImageElem(n.paintImgIdElem));
                            o.avpw_prevURL = e, a.controlsWidget_onHiResImageSaved(e)
                        } else t.Code && t.JobStatus ? a.errorNotify("ERROR_SAVING_HI_RES", [t.Code, t.JobStatus]) : a.errorNotify("ERROR_SAVING", [a.build.asyncImgrecvBase, t]), a.controlsWidgetInstance.saving = !1
                    },
                    u = function (e, t) {
                        var n, o, i, r, s = "";
                        if ("string" == typeof e)
                            if (-1 === e.indexOf("---FEATHER-POSTMESSAGE---")) try {
                                o = e.split("<url>")[1].split("</url>")[0]
                            } catch (c) {
                                a.errorNotify("ERROR_SAVING", ["Error parsing response."])
                            } else n = e.split("---FEATHER-POSTMESSAGE---"), o = n[0], i = n[1];
                        else if (r = avpw$(e).find("error"), r.length > 0) s = r.text();
                        else {
                            var u = avpw$(e).find("url");
                            u && (o = u.text(), o = o.replace(/^\s+|\s+$/g, ""));
                            var d = avpw$(e).find("hiresurl");
                            d && (i = d.text(), i = i.replace(/^\s+|\s+$/g, ""))
                        }
                        var p = [o, i, s];
                        "function" == typeof t ? t.apply(this, p) : l.apply(this, p)
                    },
                    d = function (t, n) {
                        var o = this,
                            i = (a.launchData.fileFormat || "").toLowerCase();
                        if (!i || i.length < 1) {
                            var r = o.origURL;
                            i = r ? e(r, "image/png") : "image/png"
                        } else i && -1 === i.indexOf("image/") && (i = "image/" + i);
                        var d = a.S3Uploader.isCapable(i);
                        o.paintWidget.exportImage(i, function (e) {
                            function r() {
                                a.controlsWidgetInstance.serverMessaging.sendMessage({
                                    id: "avpw_save_form",
                                    action: a.build.imgrecvServer,
                                    method: "POST",
                                    announcer: a.build.featherTargetAnnounce,
                                    origin: a.build.imgrecvBase,
                                    keyValues: a.util.extend(s(), {
                                        file: p,
                                        sessionid: a.usageTracker.getUUID(),
                                        actionlist: o.paintWidget.actions.exportJSON(!0),
                                        origurl: f,
                                        hiresurl: a.launchData.hiresUrl,
                                        contenttype: a.launchData.fileFormat,
                                        jpgquality: a.launchData.jpgQuality,
                                        debug: a.launchData.debug,
                                        asyncsave: a.launchData.asyncSave,
                                        usecustomfileexpiration: a.launchData.useCustomFileExpiration,
                                        encodedas: "base64text"
                                    }),
                                    callback: function () {
                                        (n || u).apply(this, arguments)
                                    }
                                })
                            }
                            var p, g = e.indexOf(";", 0),
                                h = e.indexOf(",", g);
                            e.slice(5, g), p = e.slice(h + 1), e = "";
                            var f = o.origURL;
                            f && 0 === f.indexOf("data:") && (f = ""), d ? a.S3Uploader.upload(i, p, function (e, n) {
                                e && r(), t ? c.call(this, n, null, e) : l.call(this, n, null, e)
                            }) : r()
                        })
                    },
                    p = function (e, t, n) {
                        var a = Math.round(t * n / 1e6 * 10) / 10,
                            i = ["didHitAzure:" + (e ? "Yes" : "No"), " width:" + t, " height:" + n, " megaPixels:" + a].join("");
                        o.trigger("usage:submit", "saveHiRes", i)
                    },
                    g = function () {
                        var e = a.paintWidgetInstance.getScaledSize(),
                            o = function () {
                                if (p(!0, e.hiresWidth, e.hiresHeight), !a.launchData.hiresUrl) return void a.errorNotify("ERROR_MISSING_HI_RES_URL");
                                var o = a.util.extend(s(), {
                                    actionlist: this.paintWidget.actions.exportJSON(!0),
                                    origurl: a.launchData.hiresUrl,
                                    fileformat: a.launchData.fileFormat,
                                    notificationmethod: "GET",
                                    backgroundcolor: "0xffffffff",
                                    jpgquality: a.launchData.jpgQuality
                                }),
                                    l = function (e) {
                                        !e || e && "JobFailed" === e.JobStatusCode ? (c(null, e), t.clearInterval(n)) : e && "JobCompleted" === e.JobStatusCode && (t.clearInterval(n), c(e.JobStatus)), i = !0
                                    },
                                    u = function (e) {
                                        var o;
                                        e && e.JobId ? (o = e.JobId, n = t.setInterval(function () {
                                            i && a.controlsWidgetInstance.serverMessaging.sendMessage({
                                                id: "avpw_save_form",
                                                action: a.build.asyncImgrecvGetJobStatus,
                                                method: "POST",
                                                dataType: "json",
                                                announcer: a.build.asyncFeatherTargetAnnounce,
                                                origin: a.build.asyncImgrecvBase,
                                                keyValues: a.util.extend(s(), {
                                                    jobid: o
                                                }),
                                                callback: l
                                            }), i = !1
                                        }, r)) : c(null, e)
                                    };
                                a.controlsWidgetInstance.serverMessaging.sendMessage({
                                    id: "avpw_save_form",
                                    action: a.build.asyncImgrecvCreateJob,
                                    method: "POST",
                                    dataType: "json",
                                    announcer: a.build.asyncFeatherTargetAnnounce,
                                    origin: a.build.asyncImgrecvBase,
                                    keyValues: o,
                                    callback: u
                                })
                            },
                            l = this,
                            u = function () {
                                d.apply(l, [!0, function (t, n) {
                                    p(!1, e.hiresWidth, e.hiresHeight), c.apply(this, arguments)
                                }])
                            },
                            g = -1 !== navigator.userAgent.indexOf("MSIE");
                        if (g || a.launchData.forceHiResSave || a.launchData.hiresUrl !== a.launchData.url) o.call(this);
                        else {
                            var e = a.paintWidgetInstance.getScaledSize(),
                                h = a.launchData.maxSize,
                                f = a.launchData.hiresWidth,
                                v = a.launchData.hiresHeight;
                            f && v ? f > h && v > h ? o.call(this) : e.hiresWidth < h && e.hiresHeight < h ? u.call(this) : o.call(this) : o.call(this)
                        }
                    },
                    h = function (e, t, n) {
                        var o = this;
                        o.paintWidget.exportImage(n, function (n) {
                            var o = a.controlsWidgetInstance;
                            o.saving = !1, o.layoutNotify(a.launchData.openType, "enableControls", [!0]), o.paintWidget.showWaitThrobber(!1), e && "function" == typeof e && (t ? t = e(n) === !1 ? !1 : !0 : e(n)), t && a.util.nextFrame(function () {
                                a.controlsWidget_onImageSaved(o.origURL)
                            })
                        })
                    },
                    f = function (e, t, n, o) {
                        var i = !a.featherUseFlash;
                        i && this.layoutNotify(a.launchData.openType, "disableControls"), this.paintWidget.showWaitThrobber(i, function () {
                            switch (e) {
                                case "saveHiRes":
                                    g.call(r);
                                    break;
                                case "getImageData":
                                    h.call(r, t, n, o);
                                    break;
                                default:
                                    d.call(r)
                            }
                        });
                        var r = this;
                        return !1
                    };
                return function (e, t, n, i) {
                    return a.controlsWidgetInstance.loaderPhase < 2 ? !1 : this.saving ? !1 : (o.trigger("tool:commit"), o.trigger("tool:close"), this.saving = !0, a.prevActionList = this.paintWidget.actions.exportJSON(!0), a.launchData.postData && "string" != typeof a.launchData.postData && (a.launchData.postData = a.JSON.stringify(a.launchData.postData)), f.apply(this, [e, t, n, i]))
                }
            }(), a.ControlsWidget.prototype.onSaveButtonClicked = function (e) {
                if (o.trigger("usage:submit", "saveclicked"), "function" == typeof a.launchData.onSaveButtonClicked) {
                    var t = a.launchData.onSaveButtonClicked.apply(a.launchData, [a.util.getImageId(a.controlsWidgetInstance.paintImgIdElem)]);
                    if (t === !1) return !1
                }
                return a.controlsWidgetInstance.save()
            }, a.ControlsWidget.prototype.showAreYouSure = function () {
                this.messager.show("avpw_aviary_quitareyousure", !0)
            }, a.ControlsWidget.prototype.cancel = function (e) {
                this.quitCount++;
                var t = this.quitCount > 0 && this.paintWidget && this.paintWidget.dirty;
                if ("function" == typeof a.launchData.onCloseButtonClicked) {
                    var n = a.launchData.onCloseButtonClicked.apply(a.launchData, [t]);
                    if (n === !1) return !1
                }
                return t ? this.showAreYouSure() : a.paintWidgetCloser(), !1
            }, a.ControlsWidget.prototype.getScaledDims = function (e, t) {
                //return 1.0;
                 return a.util.getScaledDims(e, t, this.maxWidth, this.maxHeight)
            }, a.TransformStyle = function (e) {
                var t = this,
                    n = e || "";
                return t.set = function (e) {
                    if (n)
                        for (var t in e) {
                            var a = t + "\\([^\\)]*",
                                o = new RegExp(a),
                                i = !1,
                                r = function (n, a, o) {
                                    return i = !0, t + "(" + e[t]
                                }; 
                            n = n.replace(o, r), i || (n += " " + t + "(" + e[t] + ")")
                        } else
                        for (var t in e) n += " " + t + "(" + e[t] + ")"
                }, t.serialize = function () {
                    return n
                }, t
            }, e
        }(this, "undefined" != typeof window ? window : {}, "undefined" != typeof document ? document : {}), AV.errorNotify = function (e, t) {
            var n = {
                BAD_IMAGE: {
                    code: 1,
                    message: "There was a problem loading your image provided to the `image` config key. Either it's not actually an image file or it doesn't really exist."
                },
                UNSUPPORTED: {
                    code: 2,
                    message: "It looks like you're using a browser that doesn't support the HTML canvas element (and also doesn't have Flash installed either). Please try accessing this page through a modern browser like Chrome, Firefox, Safari, or Internet Explorer (version 9 or higher). Your internets will thank you."
                },
                BAD_URL: {
                    code: 3,
                    message: "There was a problem loading the image URI provided to the ???url??? config key. Please verify that the URI is publicly accessible, and that the image is a supported format."
                },
                UNSUPPORTED_TOOL: {
                    code: 4,
                    message: "So sorry, but this tool is not available because it is not part of the set chosen with the `tools` config key. It's alternatively possible that your browser does not support this specific tool."
                },
                IMAGE_NOT_CLEAN: {
                    code: 5,
                    message: "Uh oh! We can't edit this image because the editor wasn't set up correctly to load external files via their address. You must either provide images from the same domain as the web page with the editor OR pass the external image address via the `url` config key in order for Aviary to be able to get permission from the browser to open it for editing. Sorry for the inconvenience!"
                },
                UNSUPPORTED_FONT: {
                    code: 6,
                    message: "So sorry, but this font looks to be unsupported by your browser or platform"
                },
                ERROR_SAVING: {
                    code: 7,
                    message: "There was a problem saving your photo. Please try again."
                },
                NO_APIKEY: {
                    code: 8,
                    message: "apiKey is required and not provided. See http://www.aviary.com/web-documentation#constructor-config-apikey."
                },
                ERROR_AUTHENTICATING: {
                    code: 9,
                    message: "There was a problem retrieving access to content from our server. Please ensure all authentication keys are present or do not attempt premium partner authentication."
                },
                ERROR_BAD_THEME: {
                    code: 10,
                    message: "Selected theme does not exist. Please use 'dark', 'light' or 'minimum' or see aviary.com/web for more info."
                },
                ERROR_BAD_IMAGE_WITHOUT_CORS: {
                    code: 11,
                    message: "The image URL you are trying to use is either not on the same domain or is not configured for CORS. See http://enable-cors.org/ for more info."
                },
                ERROR_SERVER_MESSAGING: {
                    code: 12,
                    message: "Error reaching Aviary services."
                },
                ERROR_BAD_AUTHENTICATION_PARAMETERS: {
                    code: 13,
                    message: "Invalid authenticationURL response. Please check the formatting the response."
                },
                BAD_FORCECROP_TOOLS: {
                    code: 14,
                    message: "If you're using the 'Force Crop' tool, the editor will disable the resize, orientation and crop tools so the user cannot change the intended forced size. It looks like the only tools you have enabled disabled by 'Force Crop'.."
                },
                ERROR_GET_ASSETS: {
                    code: 15,
                    message: "Error getting assets. Please check your authentication parameters."
                },
                ERROR_MISSING_HI_RES_URL: {
                    code: 16,
                    message: "Missing 'hiresUrl' from launch() method"
                },
                ERROR_WEBGL_LOST_CONTEXT: {
                    code: 17,
                    message: "WebGL Error: the GL lost context."
                },
                ERROR_SAVING_HI_RES: {
                    code: 18,
                    message: "There was a problem saving your photo."
                },
                UNSUPPORTED_FILE_FORMAT: {
                    code: 19,
                    message: "`fileFormat` parameter only supports `png` or `jpg`."
                }
            },
                a = function (e) { },
                o = n[e],
                i = o.message;
            return "function" == typeof AV.launchData.onError && (o.args = t, i = AV.launchData.onError(o) || i), i && a(i), i
        },
        function (e, t, n) {
            "use strict";
            e.AV = e.AV || {};
            var a = e.AV,
                o = a.Events;
            e.Aviary = a, a.feather_loaded = !1, a.feather_loading = !1, a.build = a.build || {
                version: "",
                imgrecvServer: "imgrecvserver",
                flashGatewayServer: "",
                imgrecvBase: "",
                inAppPurchaseFrameURL: "",
                imgtrackServer: "imgtrackserver",
                filterServer: "",
                jsonp_imgserver: "",
                featherTargetAnnounce: "feather_target_announce_v3.html",
                proxyServer: "",
                feather_baseURL: "",
                feather_stickerURL: "",
                googleTracker: ""
            }, a.defaultTools = ["enhance", "effects", "frames", "overlays", "overlay", "orientation", "crop", "resize", "lighting", "color", "sharpness", "focus", "vignette", "blemish", "whiten", "redeye", "drawing", "colorsplash", "textwithfont", "meme"], a.glSupportedTools = ["enhance", "effects", "frames", "overlays", "overlay", "orientation", "crop", "resize", "lighting", "color", "sharpness", "focus", "vignette", "blemish", "whiten", "redeye", "drawing", "colorsplash", "textwithfont", "meme"], a.flashSupportedTools = ["enhance", "effects", "overlay", "crop", "resize", "orientation", "brightness", "contrast", "saturation", "sharpness", "drawing", "text", "redeye", "blemish"];
            var i = {};
            return i.image = null, i.apiKey = void 0, i.apiVersion = 4, i.appendTo = null, i.language = "en", i.theme = null, i.minimumStyling = !1, i.maxSize = 1024, i.noCloseButton = !1, i.launchDelay = 300, i.closeDelay = 300, i.forceCropPreset = null, i.forceHiResSave = !1, i.authenticationURL = null, i.tools = void 0, i.initTool = "", i.cropPresets = ["Custom", "Original", ["Square", "1:1"], "3:2", "3:5", "4:3", "4:5", "4:6", "5:7", "8:10", "16:9"], i.cropPresetDefault = "Custom", i.cropPresetsStrict = !1, i.url = null, i.enableCORS = !1, i.postUrl = void 0, i.postData = null, i.fileFormat = "", i.jpgQuality = 100, i.displayImageSize = !1, i.hiresMaxSize = 1e4, i.hiresWidth = null, i.hiresHeight = null, i.onLoad = void 0, i.onReady = void 0, i.onSaveButtonClicked = void 0, i.onSave = void 0, i.onSaveHiRes = void 0, i.onClose = void 0, i.onError = void 0, i.signature = null, i.timestamp = null, i.hiresUrl = void 0, i.isPremiumPartner = !0, i.useCustomFileExpiration = !1, i.allowInAppPurchase = !1, i.disableWebGL = !1, i.forceFlash = !1, i.forceSupport = !1, i.poweredByURL = "http://www.aviary.com", i.giveFeedbackURL = "http://support.aviary.com/", i.getWidgetURL = "https://creativesdk.adobe.com", i.debug = !1, i.asyncSave = !0, a.baseConfig = i,
                function (e) {
                    var t = function (e) {
                        return {
                            language: e.Feather_Language,
                            forceFlash: e.Feather_ForceFlash,
                            forceSupport: e.AV_Feather_ForceSupport,
                            onLoad: e.Feather_OnLoad,
                            onReady: e.Feather_OnLaunchComplete,
                            onClose: e.Feather_OnClose,
                            onSave: e.Feather_OnSave,
                            noCloseButton: e.Feather_NoCloseButton,
                            maxSize: e.Feather_MaxSize || e.Feather_MaxDisplaySize,
                            tools: e.Feather_EditOptions,
                            cropPresets: e.Feather_CropSizes,
                            resizePresets: e.Feather_ResizeSizes,
                            apiKey: e.Feather_APIKey,
                            hiresUrl: e.Feather_HiResURL,
                            postUrl: e.Feather_PostURL,
                            fileFormat: e.Feather_FileFormat || e.Feather_ContentType,
                            jpgQuality: e.Feather_FileQuality,
                            signature: e.Feather_Signature,
                            timestamp: e.Feather_Timestamp
                        }
                    };
                    if (a.baseConfig = a.util.extend(a.baseConfig, t(e)), "https:" === e.location.protocol || "chrome-extension:" === e.location.protocol) {
                        var n, o;
                        for (var i in a.build) a.build.hasOwnProperty(i) && (o = i.split("_SSL"), 2 === o.length && a.build[i] && (n = o[0], a.build[n] = a.build[i]))
                    }
                }(t), a.getLocalizedString = function (e) {
                    try {
                        var t = a.lang[a.launchData.language][e];
                        return void 0 === t && (t = e), t
                    } catch (n) { }
                    return e
                }, Function.prototype.AV_bindInst = function (e) {
                    var t = this;
                    return function () {
                        return t.apply(e, arguments)
                    }
                }, a.injectControls = function () {
                    var e, t;
                    if ("popup" === a.launchData.openType ? (e = "", t = a.template[a.launchData.layout].saveBlock()) : (e = a.template[a.launchData.layout].saveBlock(), t = ""), a.criticalLayoutStyles && !a.feather_loaded) {
                        var o = n.createElement("style");
                        o.type = "text/css";
                        var i = n.createTextNode(a.criticalLayoutStyles);
                        o.styleSheet ? o.styleSheet.cssText = i.nodeValue : o.appendChild(i);
                        var r = n.getElementsByTagName("head")[0];
                        r.appendChild(o)
                    }
                    var s = a.template[a.launchData.layout].controls({
                        internalSaveBlock: e,
                        externalSaveBlock: t
                    }),
                        l = n.createElement("div");
                    l.id = "avpw_holder";
                    var c = "";
                    a.featherUseFlash && (c = "avpw_flash "), a.msie && (c += "avpw_ie" + a.msie), c && (l.className = c);
                    var u = n.getElementsByTagName("body");
                    u && (u = u[0]), u || (u = n.documentElement), u.appendChild(l), l.innerHTML = s
                }, a.Feather = function (e) {
                    e || (e = {});
                    var i = this;
                    e.authenticationURL && (e.isPremiumPartner = !0, o.on("auth:update", function (e) {
                        r(e)
                    }));
                    var r = function (n) {
                        t.avpw$.ajax({
                            url: e.authenticationURL,
                            cache: !1,
                            type: "GET",
                            contentType: "application/json"
                        }).done(function (e) {
                            for (var t, o = ["salt", "timestamp", "signature", "encryptionMethod"], r = [], s = 0; s < o.length; s++) t = o[s], e[t] || r.push(t);
                            r.length > 0 && a.errorNotify("ERROR_BAD_AUTHENTICATION_PARAMETERS", [r.join(", ")]), i.updateConfig({
                                salt: e.salt,
                                timestamp: e.timestamp,
                                signature: e.signature,
                                encryptionMethod: e.encryptionMethod
                            }), n && n()
                        })
                    },
                        s = function () {
                            a.injectControls(), a.util.nextFrame(a.loadStageFinal)
                        },
                        l = function () {
                            "undefined" != typeof avpw$ ? t.avpw$(n).ready(s) : s()
                        };
                    e && (e.openType = "aviary", e.layout = "desktop"), e = e || {}, a.launchData = a.util.extend(a.baseConfig, e);
                    var c = function () {
                        function e(e) {
                            var t = a.build.feather_baseURL + "css/" + e;
                            a.util.loadFile(t + ".css", "css")
                        }
                        var t, n;
                        if (a.featherUseFlash = !a.featherCanvasOk() && a.featherFlashOk(), a.featherGLEnabled = a.launchData.forceGL || a.shouldUseWebGL(), a.featherUseFlash && (a.launchData.forceHiResSave = !0), a.launchData.language = a.launchData.language.toLowerCase(), !a.feather_loaded && !a.feather_loading) {
                            switch (a.feather_loading = !0, t = a.launchData.language || "en", n = "js/feathercontrols_", n += a.validLanguages && a.validLanguages[t] ? t + ".js" : "en.js", !a.launchData.theme && a.launchData.minimumStyling && (a.launchData.theme = "minimum"), "minimum" === a.launchData.theme && (a.launchData.minimumStyling = !0), a.launchData.theme || (a.launchData.theme = "dark"), a.launchData.theme) {
                                case "dark":
                                case "light":
                                case "minimum":
                                    break;
                                default:
                                    a.errorNotify("ERROR_BAD_THEME")
                            }
                            var o;
                            o = a.launchData.minimumStyling ? "feather_core_" : "feather_theme_aviary_", o += a.launchData.theme, e(o), a.build.bundled ? l() : a.util.loadFile(a.build.feather_baseURL + n, "js", l)
                        }
                    };
                    c();
                    var u = function () {
                        return a.paintWidgetInstance ? !1 : void a.paintWidgetLauncher(a.launchData.image, a.launchData.url)
                    };
                    return i.launch = function (e) {
                        if (!a.feather_loaded) return !1;
                        var t = n.getElementById("avpw_holder");
                        if (t || a.injectControls(), a.paintWidgetInstance) {
                            if (t) return !1;
                            i.close(!0)
                        }
                        if (a.launchData && (a.launchData.hiresWidth || a.launchData.hiresHeight) && (a.launchData.hiresWidth = null, a.launchData.hiresHeight = null), e && e.language && delete e.language, a.launchData = e ? a.util.extend(a.launchData, e) : a.launchData, !a.launchData.image) return a.errorNotify("BAD_IMAGE"), !1;
                        if (!a.launchData.apiKey) return a.errorNotify("NO_APIKEY"), !1;
                        if ("png" !== a.launchData.fileFormat && "jpg" !== a.launchData.fileFormat && "" !== a.launchData.fileFormat && (a.launchData.fileFormat = ""), "object" == typeof e.forceCropPreset ? (a.launchData.forceCropPreset = [e.forceCropPreset], a.launchData.initTool = "forcecrop") : a.launchData.forceCropPreset = null, a.launchData.initTool && (t.className += " avpw_init_hide"), a.featherUseFlash) u();
                        else {
                            if (!a.featherSupported()) return a.errorNotify("UNSUPPORTED") && (a.controlsWidgetInstance = new a.ControlsWidget, a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "placeControls", [a.util.getApiVersion(a.launchData) > 1 ? a.launchData.appendTo : void 0]), a.controlsWidgetInstance.bindControls(), n.getElementById("avpw_controls").style.display = "block", a.controlsWidgetInstance.messager.show("avpw_aviary_unsupported", !0)), !0;
                            a.util.nextFrame(u)
                        }
                        return !0
                    }, i.showWaitIndicator = function () {
                        return a.controlsWidgetInstance && a.controlsWidgetInstance.showWaitThrobber ? (a.controlsWidgetInstance.showWaitThrobber(!0), !0) : !1
                    }, i.hideWaitIndicator = function () {
                        return a.controlsWidgetInstance && a.controlsWidgetInstance.showWaitThrobber ? (a.controlsWidgetInstance.showWaitThrobber(!1), !0) : !1
                    }, i.getImageDimensions = function () {
                        var e = null;
                        return a.paintWidgetInstance && (e = a.paintWidgetInstance.getScaledSize(), a.launchData.hiresWidth && a.launchData.hiresHeight || (delete e.hiresWidth, delete e.hiresHeight)), e
                    }, i.save = function () {
                        return a.paintWidgetInstance ? a.controlsWidgetInstance.save() : !1
                    }, i.saveHiRes = function () {
                        return a.paintWidgetInstance ? a.launchData.authenticationURL ? (o.trigger("auth:update", function () {
                            a.controlsWidgetInstance.save("saveHiRes")
                        }), !0) : a.controlsWidgetInstance.save("saveHiRes") : !1
                    }, i.getImageData = function (e, t, n) {
                        return a.paintWidgetInstance ? a.controlsWidgetInstance.save("getImageData", e, t, n) : !1
                    }, i.close = function (e) {
                        return a.paintWidgetInstance ? void a.paintWidgetCloser(e) : !1
                    }, i.relaunch = function () {
                        return o.trigger("usage:interact", "api", "relaunch"), a.isRelaunched = !0, a.launchData ? void i.launch(a.launchData) : !1
                    }, i.activateTool = function (e) {
                        o.trigger("tool:open", e, a.controlsWidgetInstance)
                    }, i.replaceImage = function (e) {
                        return o.trigger("usage:interact", "api", "replaceImage"), a.launchData ? (i.close(!0), void a.util.nextFrame(function () {
                            a.launchData.url = e, i.launch(a.launchData)
                        })) : !1
                    }, i.updateConfig = function (e, t) {
                        if (!a.launchData) return !1;
                        if (e && "object" == typeof e)
                            for (var n in e) e.hasOwnProperty(n) && (a.launchData[n] = e[n]);
                        else {
                            if (!e || "string" != typeof e) return !1;
                            a.launchData[e] = t
                        }
                    }, i.getIsDirty = function () {
                        return a.paintWidgetInstance ? a.paintWidgetInstance.dirty : !1
                    }, i.getActionList = function () {
                        return a.paintWidgetInstance ? (o.trigger("tool:commit"), a.paintWidgetInstance.actions.exportJSON(!0)) : void 0
                    }, i.disableControls = function () {
                        a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "disableControls")
                    }, i.enableControls = function () {
                        a.controlsWidgetInstance.layoutNotify(a.launchData.openType, "enableControls")
                    }, i.on = function (e, t) {
                        o && e && t && "function" == typeof t && o.on(e, t)
                    }, i.off = function (e, t) {
                        o && e && t && "function" == typeof t && o.off(e, t)
                    }, i
                }, a.loadStageFinal = function () {
                    a.feather_loaded = !0;
                    var e = function () {
                        "function" == typeof a.launchData.onLoad && a.launchData.onLoad()
                    };
                    a.launchData.authenticationURL ? o.trigger("auth:update", e) : e()
                }, a.featherSupported = function () {
                    return a.featherCanvasOk() || a.featherFlashOk() || a.launchData.forceSupport
                }, a.featherFlashOk = function () {
                    return a.launchData.forceFlash ? !0 : t.avpw_swfobject && t.avpw_swfobject.hasFlashPlayerVersion(a.build.MINIMUM_FLASH_PLAYER_VERSION)
                }, a.featherCanvasOk = function () {
                    if (a.launchData.forceFlash) return !1;
                    var e = !!n.createElement("canvas").getContext,
                        o = "function" == typeof t.postMessage;
                    return e && o
                }, a.shouldUseWebGL = function () {
                    if (a.launchData.disableWebGL) return !1;
                    var e = !!navigator.userAgent.match(/Trident.*rv[ :]*11\./);
                    if (-1 === navigator.userAgent.indexOf("Chrome") && -1 === navigator.userAgent.indexOf("Firefox") && !e) return !1;
                    if (-1 !== navigator.userAgent.indexOf("Firefox")) {
                        var o = parseInt(navigator.userAgent.toLowerCase().split("firefox/")[1]);
                        if (!o || 33 > o) return !1
                    }
                    var i = n.createElement("canvas");
                    if ("undefined" == typeof t.WebGLRenderingContext) return !1;
                    if (!i || !i.getContext) return !1;
                    var r = i.getContext("webgl") || i.getContext("experimental-webgl");

                    if (!r) return !1;
                    var s = r.getParameter(r.MAX_TEXTURE_SIZE);
                    return a.launchData.maxSize > s ? !1 : !0
                }, a.getFlashMovie = function (e) {
                    var a = t[e] || n[e];
                    return a
                }, a.msie = function () {
                    for (var e, t = 3, a = n.createElement("div"), o = a.getElementsByTagName("i") ; a.innerHTML = "<!--[if gt IE " + ++t + "]><i></i><![endif]-->", o[0];);
                    return t > 4 ? t : e
                }(), a.firefox = function () {
                    var e;
                    return "Gecko" === t.navigator.product && (e = navigator.userAgent.split("Firefox/")[1], e = parseInt(e, 10)), e
                }(), a.PAGE_WIDTH = 360, a.setPageWidth = function (e) {
                    a.PAGE_WIDTH = e
                }, e
        }(this, "undefined" != typeof window ? window : {}, "undefined" != typeof document ? document : {}), AV.S3Uploader = function () {
            function e(e, t) {
                e = atob(e);
                for (var n = new ArrayBuffer(e.length), a = new Uint8Array(n), o = 0; o < e.length; o++) a[o] = e.charCodeAt(o);
                return new Blob([n], {
                    type: t
                })
            }
            var t = {},
                n = function () {
                    return AV.build.imgrecvBase + "s3signature"
                },
                a = function (e, t) {
                    var a = new XMLHttpRequest;
                    a.open("GET", n() + "?object_type=" + e), a.onreadystatechange = function () {
                        return 4 === a.readyState ? 200 === a.status ? t(null, JSON.parse(a.responseText)) : t("Could not get signed URL") : void 0
                    }, a.send()
                };
            return t.isCapable = function (e) {
                return "image/png" !== e && "image/jpg" !== e && "image/jpeg" !== e || "function" != typeof XMLHttpRequest || "function" != typeof Blob || avpw$.browser.msie || AV.util.isIE11() ? !1 : !0
            }, t.upload = function (t, n, o) {
                var i = e(n, t);
                a(t, function (e, t) {
                    if (e || !t) return void 0;
                    var n = new XMLHttpRequest;
                    n.open("PUT", t.signed_request), n.setRequestHeader("x-amz-acl", "public-read"), n.onload = function () {
                        200 === n.status && o(null, t.url)
                    }, n.onerror = function () {
                        o("Could not upload file")
                    }, n.send(i)
                })
            }, t
        }(), AV.support = function (e) {
            var t, n = e.navigator.userAgent,
                a = e.screen.width,
                o = (e.screen.height, {
                    0: /Android/i,
                    1: /webOS/i,
                    2: /iPhone/i,
                    3: /iPod/i,
                    4: /BlackBerry/i,
                    5: /iPad/i
                }),
                i = 0,
                r = 0,
                s = 0,
                l = 0;
            for (var c in o) n.match(o[c]) && (i = 1, t = parseInt(c));
            if (n.match(/AppleWebKit/i) && (r = 1), 0 === t && (s = 1), 1 === s) {
                var u = n.match(/Android [0-9].[0-9]/).toString();
                u && (l = parseFloat(u.split("Android ")[1]))
            }
            var d = {};
            return d.isAppleWebkit = function () {
                return 1 === r
            }, d.isMobileWebkit = function () {
                return 1 === r && a && (480 >= a || l > 0 && 2.3 >= l)
            }, d.isIPhoneOrIPod = function () {
                return 2 === t || 3 === t
            }, d.isAndroid = function () {
                return 1 === s
            }, d.getAndroidVersion = function () {
                return l
            }, d.getVendorProperty = function () {
                var e = {},
                    t = function (e, t) {
                        var n, a, o = ["webkit", "ms", "Moz", "O"],
                            i = e.style;
                        if (void 0 !== i[t]) return t;
                        for (t = t.charAt(0).toUpperCase() + t.slice(1), a = 0; a < o.length; a++)
                            if (n = o[a] + t, void 0 !== i[n]) return n
                    };
                return function (n) {
                    return e[n] || (e[n] = t(document.createElement("div"), n))
                }
            }(), d
        }(window)
}(window.AV || (window.AV = {}), window, document);