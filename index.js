(function () {
  "use strict";
  var g = function () {
      var t = this,
        e = t.$createElement,
        n = t._self._c || e;
      return n(
        "div",
        [
          n(
            "k-grid",
            { staticStyle: { gap: "0.25rem", "--columns": "12" } },
            [
              n(
                "k-input",
                t._b(
                  {
                    staticStyle: { "--width": "1/3" },
                    attrs: { type: "text" },
                    on: { input: t.onInput },
                    model: {
                      value: t.content.name,
                      callback: function (i) {
                        t.$set(t.content, "name", i);
                      },
                      expression: "content.name",
                    },
                  },
                  "k-input",
                  t.field("name"),
                  !1
                )
              ),
              t.loading
                ? n("k-box", {
                    staticStyle: { "--width": "2/3" },
                    attrs: {
                      theme: "info",
                      icon: "loader",
                      text: t.$t("form.block.inbox.loading"),
                    },
                  })
                : t.status.storageEnabled
                ? n("k-box", {
                    staticStyle: { "--width": "2/3" },
                    attrs: {
                      icon: "email",
                      theme: t.status.theme,
                      text:
                        t.$t("form.block.inbox.show") +
                        " (" +
                        t.status.text +
                        ")",
                    },
                    nativeOn: {
                      click: function (i) {
                        return t.open.apply(null, arguments);
                      },
                    },
                  })
                : n("k-box", {
                    staticStyle: { "--width": "2/3" },
                    attrs: {
                      icon: "cancel",
                      theme: "info",
                      text: t.$t("form.block.inbox.storage.disabled"),
                    },
                    nativeOn: {
                      click: function (i) {
                        return t.open.apply(null, arguments);
                      },
                    },
                  }),
            ],
            1
          ),
        ],
        1
      );
    },
    b = [];
  function d(t, e, n, i, a, l, m, W) {
    var s = typeof t == "function" ? t.options : t;
    e && ((s.render = e), (s.staticRenderFns = n), (s._compiled = !0)),
      i && (s.functional = !0),
      l && (s._scopeId = "data-v-" + l);
    var r;
    if (
      (m
        ? ((r = function (o) {
            (o =
              o ||
              (this.$vnode && this.$vnode.ssrContext) ||
              (this.parent &&
                this.parent.$vnode &&
                this.parent.$vnode.ssrContext)),
              !o &&
                typeof __VUE_SSR_CONTEXT__ != "undefined" &&
                (o = __VUE_SSR_CONTEXT__),
              a && a.call(this, o),
              o && o._registeredComponents && o._registeredComponents.add(m);
          }),
          (s._ssrRegister = r))
        : a &&
          (r = W
            ? function () {
                a.call(
                  this,
                  (s.functional ? this.parent : this).$root.$options.shadowRoot
                );
              }
            : a),
      r)
    )
      if (s.functional) {
        s._injectStyles = r;
        var G = s.render;
        s.render = function (K, v) {
          return r.call(v), G(K, v);
        };
      } else {
        var _ = s.beforeCreate;
        s.beforeCreate = _ ? [].concat(_, r) : [r];
      }
    return { exports: t, options: s };
  }
  const k = {
      data() {
        return {
          migrate: !1,
          loading: !0,
          status: {
            type: Object,
            default: { count: "-", read: "-", fail: "-", state: "wait" },
          },
        };
      },
      destroyed() {
        window.panel.events.off("form.update", this.updateCount);
      },
      created() {
        window.panel.events.on("content/STATUS", function (t) {
          t.type == "content/STATUS" && window.panel.events.emit("form.update");
        }),
          (this.content.formid = this.id),
          this.updateCount(),
          window.panel.events.on("form.update", this.updateCount);
      },
      methods: {
        updateCount() {
          const t = this;
          this.$api
            .get("formblock", {
              action: "info",
              form_id: this.id,
              params: JSON.stringify({ form_name: this.content.name }),
            })
            .then((e) => {
              (t.status = e), (this.loading = !1);
            });
        },
        onInput(t) {
          this.$emit("update", t);
        },
      },
    },
    u = {};
  var $ = d(k, g, b, !1, y, null, null, null);
  function y(t) {
    for (let e in u) this[e] = u[e];
  }
  var w = (function () {
      return $.exports;
    })(),
    x = function () {
      var t = this,
        e = t.$createElement,
        n = t._self._c || e;
      return n(
        "k-dialog",
        t._b(
          {
            ref: "dialog",
            staticClass: "k-field-type-page-dialog",
            on: {
              cancel: function (i) {
                return t.$emit("cancel");
              },
              submit: function (i) {
                return t.$emit("submit");
              },
            },
          },
          "k-dialog",
          t.$props,
          !1
        ),
        [
          n("k-headline", [t._v(t._s(t.current.title))]),
          t.current.formfields
            ? n("div", [
                n(
                  "table",
                  { staticClass: "k-field-type-page-dialog-table" },
                  t._l(t.current.formfields, function (i, a) {
                    return n("tr", { key: a, class: "field_" + a }, [
                      n("td", [t._v(t._s(i))]),
                      t.current.attachment[a]
                        ? n("td", [
                            n(
                              "ul",
                              {
                                staticClass:
                                  "k-field-type-page-dialog-linklist",
                              },
                              t._l(t.current.attachment[a], function (l) {
                                return n("li", { key: l.tmp_name }, [
                                  n(
                                    "a",
                                    {
                                      staticClass:
                                        "k-field-type-page-dialog-link",
                                      attrs: {
                                        href: l.location,
                                        download: l.name,
                                      },
                                    },
                                    [
                                      n("k-icon", {
                                        attrs: { type: "attachment" },
                                      }),
                                      t._v(" " + t._s(l.name) + " "),
                                    ],
                                    1
                                  ),
                                ]);
                              }),
                              0
                            ),
                          ])
                        : n("td", [
                            t._v(" " + t._s(t.current.formdata[a]) + " "),
                          ]),
                    ]);
                  }),
                  0
                ),
              ])
            : n("div", { staticClass: "k-field-type-page-dialog-table" }, [
                t._v(" " + t._s(t.current.formdata.summary) + " "),
              ]),
          t.current.error
            ? n("k-box", {
                attrs: { text: t.current.error, theme: "negative" },
              })
            : t._e(),
        ],
        1
      );
    },
    S = [],
    Q = "";
  const C = {
      extends: "k-dialog",
      props: { current: { type: Object, default() {} } },
    },
    c = {};
  var O = d(C, x, S, !1, D, null, null, null);
  function D(t) {
    for (let e in c) this[e] = c[e];
  }
  var M = (function () {
      return O.exports;
    })(),
    E = function () {
      var t = this,
        e = t.$createElement,
        n = t._self._c || e;
      return n(
        "div",
        { staticClass: "k-mailview-list" },
        [
          t.hideheader
            ? n(
                "div",
                { staticClass: "k-mailview-toolbar" },
                [
                  n(
                    "div",
                    { staticClass: "k-mailview-storage-toggle" },
                    [
                      n("label", { staticClass: "k-mailview-storage-label" }, [
                        t._v(t._s(t.$t("form.block.inbox.storage"))),
                      ]),
                      n("k-toggle-input", {
                        attrs: { value: t.storageEnabled },
                        on: { input: t.toggleStorage },
                      }),
                    ],
                    1
                  ),
                  t.value.content.length > 0
                    ? n(
                        "k-button-group",
                        { staticClass: "k-mailview-buttons" },
                        [
                          n("k-button", {
                            attrs: {
                              icon: "cancel",
                              theme: "red",
                              text: t.$t("delete.all"),
                            },
                            on: { click: t.onDelete },
                          }),
                          n("k-button", {
                            attrs: {
                              icon: "download",
                              link: t.value.header.download,
                              text: t.$t("form.block.inbox.export"),
                              download: !0,
                            },
                          }),
                        ],
                        1
                      )
                    : t._e(),
                ],
                1
              )
            : t._e(),
          t.hideheader
            ? t._e()
            : n(
                "div",
                { staticClass: "k-mailview-list-header" },
                [
                  n("k-box", {
                    attrs: {
                      theme: t.value.header.state.theme,
                      icon: t.isOpen ? "angle-up" : "angle-down",
                      text: t.headerText,
                    },
                    nativeOn: {
                      click: function (i) {
                        return t.toggleOpen();
                      },
                    },
                  }),
                  n("k-button", {
                    attrs: {
                      icon: "download",
                      variant: "filled",
                      link: t.value.header.download,
                      theme: t.value.header.state.theme,
                      download: !0,
                    },
                  }),
                ],
                1
              ),
          t.isOpen || t.hideheader
            ? [
                t.value.content.length === 0
                  ? n("k-box", {
                      attrs: {
                        theme: "info",
                        text: t.$t("form.block.inbox.empty"),
                      },
                    })
                  : n("k-items", { attrs: { items: t.items } }),
              ]
            : t._e(),
        ],
        2
      );
    },
    R = [],
    Z = "";
  const T = {
      props: {
        value: { type: Array, required: !0 },
        showuuid: Boolean,
        hideheader: Boolean,
      },
      data() {
        return { isOpen: !1, storageEnabled: !0 };
      },
      computed: {
        items() {
          return this.value.content;
        },
        headerText() {
          return this.showuuid
            ? this.value.header.name + " (" + this.value.uuid + ")"
            : this.value.header.name;
        },
      },
      created() {
        (this.isOpen =
          sessionStorage.getItem(
            `plain.form.showOpen.${this.value.page}.${this.value.uuid}`
          ) === "on"),
          (this.storageEnabled = this.value.storageEnabled !== !1);
      },
      methods: {
        toggleOpen() {
          (this.isOpen = !this.isOpen),
            sessionStorage.setItem(
              `plain.form.showOpen.${this.value.page}.${this.value.uuid}`,
              this.isOpen ? "on" : "off"
            );
        },
        toggleStorage(t) {
          this.$api
            .get("formblock", {
              action: "setStorageEnabled",
              page_id: this.value.page,
              form_id: this.value.id,
              params: JSON.stringify({ enabled: t }),
            })
            .then(() => {
              this.storageEnabled = t;
            });
        },
        onDelete() {
          this.$panel.dialog.open({
            component: "k-remove-dialog",
            props: { text: this.$t("field.entries.delete.confirm.all") },
            on: {
              submit: () => {
                this.$api
                  .get("formblock", {
                    action: "deleteAll",
                    page_id: this.value.page,
                    form_id: this.value.id,
                  })
                  .then((t) => {
                    this.$emit("refresh"), this.$panel.dialog.close();
                  });
              },
            },
          });
        },
      },
    },
    f = {};
  var Y = d(T, E, R, !1, B, null, null, null);
  function B(t) {
    for (let e in f) this[e] = f[e];
  }
  var L = (function () {
      return Y.exports;
    })(),
    j = function () {
      var t = this,
        e = t.$createElement,
        n = t._self._c || e;
      return n(
        "div",
        { staticClass: "k-field-type-mail-view" },
        [
          t.loading
            ? n("k-box", {
                attrs: {
                  theme: "info",
                  icon: "loader",
                  text: t.$t("form.block.inbox.loading"),
                },
              })
            : n(
                "k-grid",
                {
                  style: { gap: "var(--spacing-2)" },
                  attrs: { variant: "fields" },
                },
                [
                  t._l(t.data, function (i) {
                    return n("k-mail-list", {
                      key: i.slug,
                      staticClass: "k-table k-field-type-mail-table",
                      staticStyle: { "--width": "1/1" },
                      attrs: {
                        hideheader: t.hideheader,
                        value: i,
                        showuuid: t.isUnique(i),
                      },
                      on: { refresh: t.updateList },
                    });
                  }),
                  t.data.length === 0
                    ? n("k-box", {
                        staticStyle: { "--width": "1/1" },
                        attrs: {
                          theme: "info",
                          text: t.$t("form.block.inbox.empty"),
                        },
                      })
                    : t._e(),
                ],
                2
              ),
          n("k-plain-license", { attrs: { prefix: "formblock" } }),
        ],
        1
      );
    },
    F = [];
  const N = {
      props: {
        value: { type: String, default: "" },
        dateformat: { type: String, default: "DD.MM.YYYY HH:mm" },
        forms: { type: Array, default: () => [] },
        formData: { type: Object, default: () => {} },
      },
      data() {
        return { data: [], filter: [], loading: !0, hideheader: !1 };
      },
      computed: {
        thispage() {
          return this.$attrs.endpoints.model
            .replace("/pages/", "")
            .replace(/\+/g, "/");
        },
      },
      created() {
        this.formData.formid
          ? ((this.filter = [this.formData.formid]), (this.hideheader = !0))
          : (this.filter = this.forms),
          this.updateList(),
          window.panel.events.on("form.update", this.updateList);
      },
      destroyed() {
        window.panel.events.off("form.update", this.updateList);
      },
      methods: {
        send(t, e, n) {
          var i, a;
          this.$api
            .get("formblock", {
              action: t,
              page_id: this.thispage,
              request_id: (i = e == null ? void 0 : e.request) != null ? i : "",
              form_id: (a = e == null ? void 0 : e.form) != null ? a : "",
              params: JSON.stringify(e),
            })
            .then((l) => {
              (this.loading = !1), n(l);
            });
        },
        isUnique(t) {
          return (
            this.data.filter(
              (e) =>
                t.header.page === e.header.page &&
                t.header.name === e.header.name
            ).length > 1
          );
        },
        updateList() {
          let t = this;
          this.send("requestsArray", { filter: this.filter }, (e) => {
            this.data = Object.keys(e).map(function (n) {
              return (
                (e[n].content = e[n].content.map((i) => {
                  (i.formid = n),
                    (i.attachment =
                      "attachment" in i ? JSON.parse(i.attachment) : !1),
                    (i.formdata = JSON.parse(i.formdata)),
                    (i.formfields =
                      "formfields" in i ? JSON.parse(i.formfields) : !1);
                  let a = t.$library.dayjs(i.received, "YYYY-MM-DD HH:mm:ss");
                  return (
                    (i.info = a.isValid() ? a.format(t.dateformat) : ""),
                    (i.text = t.getLabel(i)),
                    (i.image = t.getImage(i)),
                    (i.buttons = [t.getButton("info", i)]),
                    (i.options = [
                      i.read === ""
                        ? t.getButton("unread", i)
                        : t.getButton("read", i),
                      t.getButton("delete", i),
                    ]),
                    i
                  );
                })),
                e[n]
              );
            });
          });
        },
        setRead(t, e) {
          this.send(
            "update",
            {
              form: e.formid,
              request: e.slug,
              read:
                t == !1
                  ? ""
                  : this.$library.dayjs().format("YYYY-MM-DD HH:mm:ss"),
            },
            () => {
              window.panel.events.emit("form.update"),
                this.$panel.dialog.close();
            }
          );
        },
        getLabel(t) {
          return t.display
            ? t.display
            : this.value
            ? this.$helper.string.template(this.value, t.formdata)
            : t.id;
        },
        getButton(t, e) {
          return t === "delete"
            ? {
                icon: "trash",
                text: this.$t("form.block.inbox.delete"),
                click: () =>
                  this.send(
                    "delete",
                    { form: e.formid, request: e.slug },
                    () => {
                      window.panel.events.emit("form.update");
                    }
                  ),
              }
            : t === "unread"
            ? {
                icon: "preview",
                text: this.$t("form.block.inbox.asread"),
                click: () => this.setRead(!0, e),
              }
            : t === "read"
            ? {
                icon: "hidden",
                text: this.$t("form.block.inbox.asunread"),
                click: () => this.setRead(!1, e),
              }
            : {
                icon: "info",
                click: () =>
                  this.$panel.dialog.open({
                    component: "k-mail-dialog",
                    props: {
                      current: e,
                      size: "medium",
                      submitButton: e.read ? {} : this.getButton("unread", e),
                      cancelButton: e.read ? this.getButton("read", e) : {},
                    },
                  }),
              };
        },
        getImage(t) {
          return t.read
            ? { icon: "circle", color: "yellow", back: "transparent" }
            : t.error
            ? { icon: "cancel", color: "red", back: "transparent" }
            : { icon: "circle-filled", color: "green", back: "transparent" };
        },
      },
    },
    h = {};
  var A = d(N, j, F, !1, H, null, null, null);
  function H(t) {
    for (let e in h) this[e] = h[e];
  }
  var I = (function () {
      return A.exports;
    })(),
    J = function () {
      var t = this,
        e = t.$createElement,
        n = t._self._c || e;
      return t.license !== null
        ? n(
            "div",
            {
              staticClass: "k-plain-license",
              style: t.containerStyle,
              on: { click: t.showDialog },
            },
            [
              n("k-text", {
                style: t.textStyle,
                attrs: { size: "tiny", html: t.licenseText },
              }),
              n("k-icon", { style: t.iconStyle, attrs: { type: "alert" } }),
            ],
            1
          )
        : t._e();
    },
    U = [],
    q = "";
  const V = {
      props: {
        prefix: {
          type: String,
          default() {
            return null;
          },
        },
        styling: {
          type: Object,
          default() {
            return {};
          },
        },
      },
      data() {
        return { license: null };
      },
      computed: {
        licenseText() {
          return this.license
            ? `<strong>${this.license.title}</strong><br />${this.license.cta}`
            : "";
        },
        containerStyle() {
          return this.styling && this.styling.container
            ? this.styling.container
            : this.styling;
        },
        textStyle() {
          var t, e;
          return (e = (t = this.styling) == null ? void 0 : t.text) != null
            ? e
            : {};
        },
        iconStyle() {
          var t, e;
          return (e = (t = this.styling) == null ? void 0 : t.icon) != null
            ? e
            : {};
        },
      },
      created() {
        (this.license =
          window.panel.translation.data &&
          window.panel.translation.data["plain.licenses." + this.prefix]
            ? window.panel.translation.data["plain.licenses." + this.prefix]
            : null),
          (window.panel.translation.data["plain.licenses." + this.prefix] =
            null);
      },
      methods: {
        showDialog() {
          this.license &&
            this.license.dialog &&
            this.$dialog(this.license.dialog);
        },
      },
    },
    p = {};
  var z = d(V, J, U, !1, P, null, null, null);
  function P(t) {
    for (let e in p) this[e] = p[e];
  }
  var X = (function () {
    return z.exports;
  })();
  window.panel.plugin("plain/formblock", {
    fields: { mailview: I },
    components: { "k-mail-list": L, "k-mail-dialog": M, "k-plain-license": X },
    blocks: { form: w },
  });
})();
