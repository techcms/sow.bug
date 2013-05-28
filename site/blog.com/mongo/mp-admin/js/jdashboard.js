/*
 * jDashboard.js - jQuery Plugin v1.0.0
 * http://www.codecanyon.net/user/sarthemaker
 *
 * Copyright 2010, Sarathi Hansen
 *
 * Includes Cookie plugin
 * http://plugins.jquery.com/project/Cookie
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses
 *
 * Date: October 29, 2010
 */
(function(f) {
    f.cookie = function(l, s, v) {
        if (typeof s != "undefined") {
            v = v || {};
            if (s === null) {
                s = "";
                v.expires = -1
            }
            var o = "";
            if (v.expires && (typeof v.expires == "number" || v.expires.toUTCString)) {
                var p;
                if (typeof v.expires == "number") {
                    p = new Date();
                    p.setTime(p.getTime() + (v.expires * 24 * 60 * 60 * 1000))
                } else {
                    p = v.expires
                }
                o = "; expires=" + p.toUTCString()
            }
            var u = v.path ? "; path=" + (v.path) : "";
            var q = v.domain ? "; domain=" + (v.domain) : "";
            var k = v.secure ? "; secure": "";
            document.cookie = [l, "=", encodeURIComponent(s), o, u, q, k].join("")
        } else {
            var n = null;
            if (document.cookie && document.cookie != "") {
                var t = document.cookie.split(";");
                for (var r = 0; r < t.length; r++) {
                    var m = f.trim(t[r]);
                    if (m.substring(0, l.length + 1) == (l + "=")) {
                        n = decodeURIComponent(m.substring(l.length + 1));
                        break
                    }
                }
            }
            return n
        }
    };
    f.cookie("jdash-detect-cookies", "is_working");
    var d = f.cookie("jdash-detect-cookies") == "is_working";
    f.fn.jDashboard = function(k) {
        var l = {
            columns: 2,
            columnWidth: null,
            dragOpacity: 0.65,
            showSector: true
        };
        k = f.extend(l, k);
        return this.each(function() {
            var s = document.createElement("div");
            f(s).addClass("jdash-sector");
            if (!k.showSector) {
                f(s).css({
                    visibility: "hidden"
                })
            }
            var A = f(this);
            this.sector = s;
            A.addClass("jdash").append(s);
            var w = A.children("div.jdash-item");
            var C = document.createElement("table");
            var v = document.createElement("tbody");
            var q = document.createElement("tr");
            var y = [];
            for (var x = 0; x < k.columns; x++) {
                var r = document.createElement("td");
                f(r).addClass("jdash-column");
                f(r).css({
                    width: k.columnWidth == null ? Math.floor(100 / k.columns) + "%": k.columnWidth
                });
                f(q).append(r);
                y.push(r)
            }
            f(C).attr({
                cellPadding: 0,
                cellSpacing: 0
            });
            if (k.columnWidth == null) {
                f(C).css({
                    width: "100%"
                })
            }
            C.appendChild(v);
            v.appendChild(q);
            this.appendChild(C);
            if (d) {
                var n;
                if (!f.cookie("jdash-collapse-" + A.attr("id")) || f.cookie("jdash-collapse-" + A.attr("id")).split("-").length != w.length) {
                    n = "";
                    for (var t = 0; t < w.length; t++) {
                        n += (t > 0 ? "-0": "0")
                    }
                    f.cookie("jdash-collapse-" + A.attr("id"), n, {
                        expires: 365
                    })
                }
                if (!f.cookie("jdash-index-" + A.attr("id")) || f.cookie("jdash-index-" + A.attr("id")).split("|").length != w.length) {
                    n = "";
                    var o = 0;
                    for (var t = 0; t < w.length; t++) {
                        n += (t > 0 ? "|": "") + o + "," + t;
                        o++;
                        if (o >= y.length) {
                            o = 0
                        }
                    }
                    f.cookie("jdash-index-" + A.attr("id"), n, {
                        expires: 365
                    })
                }
                var z = f.cookie("jdash-collapse-" + A.attr("id")).split("-");
                var m = f.cookie("jdash-index-" + A.attr("id")).split("|")
            }
            w.each(function(H) {
                var J = f(this);
                this.jdashCollapse = false;
                this.jdashId = H;
                this.dragOpacity = k.dragOpacity;
                this.offset = {
                    click: {
                        left: 0,
                        top: 0
                    },
                    left: 0,
                    top: 0
                };
                var L = J.children("h1.jdash-head:first-child");
                var O;
                if (L.children("div.jdash-toolbar").length) {
                    O = L.children("div.jdash-toolbar")[0]
                }
                L.children("div.jdash-toolbar").remove();
                var N = L.html();
                L.html("");
                var P = document.createElement("table");
                var E = document.createElement("tbody");
                var M = document.createElement("tr");
                var G = document.createElement("td");
                var F = document.createElement("td");
                f(P).attr({
                    cellPadding: 0,
                    cellSpacing: 0
                }).css({
                    width: "100%"
                });
                P.appendChild(E);
                E.appendChild(M);
                if (O) {
                    var K = document.createElement("tr");
                    var D = document.createElement("td");
                    E.appendChild(K);
                    K.appendChild(D);
                    f(D).attr("colspan", "2");
                    D.appendChild(O)
                }
                f(G).css({
                    verticalAlign: "middle"
                });
                f(F).css({
                    width: 31,
                    verticalAlign: "middle"
                });
                M.appendChild(G);
                M.appendChild(F);
                L.append(P);
                var I = document.createElement("div");
                G.appendChild(I);
                f(I).html(N).addClass("jdash-head-title");
                var u = document.createElement("div");
                f(u).addClass("jdash-head-collapse").click(g);
                F.appendChild(u);
                f(I).mousedown(f.proxy(i, this));
                f(document).mousemove(f.proxy(j, this)).mouseup(f.proxy(e, this));
                if (d && z[H] == "1") {
                    J.children("div.jdash-body").css({
                        display: "none"
                    });
                    J.addClass("collapse");
                    this.jdashCollapse = true
                }
            });
            if (d) {
                var p = [];
                for (var B = 0; B < m.length; B++) {
                    p.push(m[B].split(",")[1])
                }
                for (var t = 0; t < w.length; t++) {
                    f(y[Math.min(m[t].split(",")[0], y.length - 1)]).append(w[m[t].split(",")[1]])
                }
            } else {
                var o = 0;
                for (var t = 0; t < w.length; t++) {
                    f(y[o]).append(w[t]);
                    o++;
                    if (o >= k.columns) {
                        o = 0
                    }
                }
            }
        })
    };
    function i(l) {
        var k = f(this);
        if (!this.isDragging && !this.dragInit) {
            this.offset.left = k.offset().left;
            this.offset.top = k.offset().top;
            this.offset.click.left = l.pageX - k.offset().left;
            this.offset.click.top = l.pageY - k.offset().top;
            this.dragInit = true;
            k.css({
                position: "relative"
            })
        }
        l.preventDefault()
    }
    function j(l) {
        var k = f(this);
        if (this.dragInit && !this.isDragging) {
            this.isDragging = true;
            this.dragInit = false;
            f.proxy(c, this)(l);
            k.css({
                opacity: this.dragOpacity
            });
            this.offset.left = k.offset().left;
            this.offset.top = k.offset().top
        }
        if (this.isDragging) {
            k.css({
                left: l.pageX - this.offset.left - this.offset.click.left,
                top: l.pageY - this.offset.top - this.offset.click.top
            });
            f.proxy(b, this)(l)
        }
        l.preventDefault()
    }
    function e(k) {
        if (this.isDragging || this.dragInit) {
            if (this.isDragging) {
                f.proxy(h, this)(k)
            }
            this.isDragging = false;
            this.dragInit = false;
            f(this).css({
                opacity: 1
            })
        }
        k.preventDefault()
    }
    function c(m) {
        var l = f(this);
        l.addClass("dragging");
        var k = l.parents("div.jdash")[0].sector;
        f(k).insertAfter(this);
        l.css({
            marginBottom: -this.offsetHeight - (l.is(":last-child") ? 0 : parseFloat(l.css("marginTop")))
        });
        f(k).css({
            display: "block",
            height: this.offsetHeight + (parseFloat(l.css("paddingTop")) - parseFloat(f(k).css("paddingTop"))) + (parseFloat(l.css("paddingBottom")) - parseFloat(f(k).css("paddingBottom"))) - parseFloat(f(k).css("borderTopWidth")) - parseFloat(f(k).css("borderBottomWidth")) - parseFloat(l.css("paddingTop")) - parseFloat(l.css("paddingBottom"))
        })
    }
    function b(m) {
        var l = f(this);
        var k = l.parents("div.jdash")[0].sector;
        var n = this;
        l.parents("tr").children("td.jdash-column").each(function() {
            var o = f(this);
            if ((o.children().length == 0 || (o.children().length == 1 && o.children()[0] == n)) && l.offset().left + n.offsetWidth / 2 - 10 > o.offset().left && l.offset().left + n.offsetWidth / 2 + 10 < o.offset().left + this.offsetWidth) {
                f(o.parents("div.jdash")[0].sector).appendTo(this);
                f.proxy(a, n)(m)
            }
        });
        l.parents("div.jdash").find("div.jdash-item").each(function() {
            var o = f(this);
            if (this != n) {
                if (l.offset().left + n.offsetWidth / 2 - 10 > o.offset().left && l.offset().left + n.offsetWidth / 2 + 10 < o.offset().left + this.offsetWidth && ((l.offset().top > o.offset().top && l.offset().top + 10 < o.offset().top + this.offsetHeight / 2) || ((o.is(":first-child") || (l.is(":first-child") && o.prev()[0] == n)) && l.offset().top + 10 < o.offset().top + this.offsetHeight / 2)) && o.prev()[0] != k) {
                    f(o.parents("div.jdash")[0].sector).insertBefore(this);
                    f.proxy(a, n)(m)
                } else {
                    if (l.offset().left + n.offsetWidth / 2 - 10 > o.offset().left && l.offset().left + n.offsetWidth / 2 + 10 < o.offset().left + this.offsetWidth && ((l.offset().top + n.offsetHeight - 10 > o.offset().top + this.offsetHeight / 2 && l.offset().top + n.offsetHeight + 10 < o.offset().top + this.offsetHeight) || ((o.is(":last-child") || (l.is(":last-child") && o.next()[0] == n)) && l.offset().top + n.offsetHeight - 10 > o.offset().top + this.offsetHeight / 2)) && o.next().next()[0] != k) {
                        f(o.parents("div.jdash")[0].sector).insertAfter(this);
                        f.proxy(a, n)(m)
                    }
                }
            }
        });
        l.css({
            marginBottom: -this.offsetHeight - (l.is(":last-child") ? 0 : parseFloat(l.css("marginTop")))
        })
    }
    function h() {
        var q = f(this);
        q.css({
            marginBottom: -this.offsetHeight - parseFloat(q.css("marginTop"))
        });
        var o = q.parents("div.jdash")[0].sector;
        q.css({
            top: q.offset().top - f(o).offset().top,
            left: q.offset().left - f(o).parent("td.jdash-column").offset().left - parseFloat(q.css("marginLeft"))
        });
        q.insertBefore(o);
        q.animate({
            left: 0,
            top: 0
        },
        "fast", "",
        function() {
            q.removeClass("dragging");
            f(o).css({
                display: "none"
            });
            q.css({
                marginBottom: parseFloat(q.css("marginTop"))
            })
        });
        if (d) {
            var k = "";
            var m = 0;
            var l = q.parents("div.jdash").find("div.jdash-item").toArray();
            var p = q.parents("div.jdash").find("td.jdash-column").length;
            for (var n = 0; n < l.length; n++) {
                k += (n > 0 ? "|": "") + f(l[n]).parents("td.jdash-column").index() + "," + l[n].jdashId;
                m++;
                if (m >= p) {
                    m = 0
                }
            }
            f.cookie("jdash-index-" + q.parents("div.jdash").attr("id"), k, {
                expires: 365
            })
        }
    }
    function a(l) {
        var k = f(this);
        this.offset.left = k.offset().left - parseFloat(k.css("left"));
        this.offset.top = k.offset().top - parseFloat(k.css("top"));
        k.css({
            left: l.pageX - this.offset.left - this.offset.click.left,
            top: l.pageY - this.offset.top - this.offset.click.top
        })
    }
    function g() {
        var p = f(this);
        var k = p.parents("div.jdash-item").children("div.jdash-body");
        if (d) {
            p.parents("div.jdash-item")[0].jdashCollapse = k.css("display") != "none";
            var l = p.parents("div.jdash").find("div.jdash-item");
            var n = "";
            for (var o = 0; o < l.length; o++) {
                for (var m = 0; m < l.length; m++) {
                    if (l[m].jdashId == o) {
                        n += (n == "" ? (l[m].jdashCollapse ? "1": "0") : "-" + (l[m].jdashCollapse ? "1": "0"));
                        break
                    }
                }
            }
            f.cookie("jdash-collapse-" + p.parents("div.jdash").attr("id"), n, {
                expires: 365
            })
        }
        k.slideToggle("fast",
        function() {
            if (k.css("display") == "none") {
                k.parents("div.jdash-item").addClass("collapse")
            }
        });
        if (k.css("display") == "block") {
            k.parents("div.jdash-item").removeClass("collapse")
        }
    }
})(jQuery);