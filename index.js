(function() {
  "use strict";
  function normalizeComponent(scriptExports, render, staticRenderFns, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render) {
      options.render = render;
      options.staticRenderFns = staticRenderFns;
      options._compiled = true;
    }
    if (functionalTemplate) {
      options.functional = true;
    }
    if (scopeId) {
      options._scopeId = "data-v-" + scopeId;
    }
    var hook;
    if (moduleIdentifier) {
      hook = function(context) {
        context = context || this.$vnode && this.$vnode.ssrContext || this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext;
        if (!context && typeof __VUE_SSR_CONTEXT__ !== "undefined") {
          context = __VUE_SSR_CONTEXT__;
        }
        if (injectStyles) {
          injectStyles.call(this, context);
        }
        if (context && context._registeredComponents) {
          context._registeredComponents.add(moduleIdentifier);
        }
      };
      options._ssrRegister = hook;
    } else if (injectStyles) {
      hook = shadowMode ? function() {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        );
      } : injectStyles;
    }
    if (hook) {
      if (options.functional) {
        options._injectStyles = hook;
        var originalRender = options.render;
        options.render = function renderWithStyleInjection(h, context) {
          hook.call(context);
          return originalRender(h, context);
        };
      } else {
        var existing = options.beforeCreate;
        options.beforeCreate = existing ? [].concat(existing, hook) : [hook];
      }
    }
    return {
      exports: scriptExports,
      options
    };
  }
  const _sfc_main$4 = {
    data() {
      return {
        migrate: false,
        loading: true,
        status: {
          type: Object,
          default: {
            count: "-",
            read: "-",
            fail: "-",
            state: "wait"
          }
        }
      };
    },
    destroyed() {
      window.panel.events.off("form.update", this.updateCount);
    },
    created() {
      this.$store.subscribe(function(mutation) {
        if (mutation.type == "content/STATUS")
          window.panel.events.emit("form.update");
      });
      this.content.formid = this.id;
      this.updateCount();
      window.panel.events.on("form.update", this.updateCount);
    },
    methods: {
      updateCount() {
        const $this = this;
        this.$api.get("formblock", {
          action: "info",
          form_id: this.id,
          params: JSON.stringify({ form_name: this.content.name })
        }).then((data) => {
          $this.status = data;
          this.loading = false;
        });
      },
      onInput(value) {
        this.$emit("update", value);
      }
    }
  };
  var _sfc_render$4 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", [_c("k-grid", { staticStyle: { "gap": "0.25rem", "--columns": "12" } }, [_c("k-input", _vm._b({ staticStyle: { "--width": "1/3" }, attrs: { "type": "text" }, on: { "input": _vm.onInput }, model: { value: _vm.content.name, callback: function($$v) {
      _vm.$set(_vm.content, "name", $$v);
    }, expression: "content.name" } }, "k-input", _vm.field("name"), false)), _vm.loading ? _c("k-box", { staticStyle: { "--width": "2/3" }, attrs: { "theme": "info", "icon": "loader", "text": _vm.$t("form.block.inbox.loading") } }) : _c("k-box", { staticStyle: { "--width": "2/3" }, attrs: { "icon": "email", "theme": _vm.status.theme, "text": _vm.$t("form.block.inbox.show") + " (" + _vm.status.text + ")" }, nativeOn: { "click": function($event) {
      return _vm.open.apply(null, arguments);
    } } })], 1)], 1);
  };
  var _sfc_staticRenderFns$4 = [];
  _sfc_render$4._withStripped = true;
  var __component__$4 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$4,
    _sfc_render$4,
    _sfc_staticRenderFns$4,
    false,
    null,
    null,
    null,
    null
  );
  __component__$4.options.__file = "/Users/romangsponer/Cloud/_sites/plugin-env/site/plugins/kirby-form-block-suite/src/components/blocks/Form.vue";
  const Form = __component__$4.exports;
  const Form_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$3 = {
    extends: "k-dialog",
    props: {
      current: {
        type: Object,
        default() {
        }
      }
    }
  };
  var _sfc_render$3 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-dialog", _vm._b({ ref: "dialog", staticClass: "k-field-type-page-dialog", on: { "cancel": function($event) {
      return _vm.$emit("cancel");
    }, "submit": function($event) {
      return _vm.$emit("submit");
    } } }, "k-dialog", _vm.$props, false), [_c("k-headline", [_vm._v(_vm._s(_vm.current.title))]), _vm.current.formfields ? _c("div", [_c("table", { staticClass: "k-field-type-page-dialog-table" }, _vm._l(_vm.current.formfields, function(label, key) {
      return _c("tr", { key, class: "field_" + key }, [_c("td", [_vm._v(_vm._s(label))]), _vm.current.attachment[key] ? _c("td", [_c("ul", { staticClass: "k-field-type-page-dialog-linklist" }, _vm._l(_vm.current.attachment[key], function(f) {
        return _c("li", { key: f.tmp_name }, [_c("a", { staticClass: "k-field-type-page-dialog-link", attrs: { "href": f.location, "download": f.name } }, [_c("k-icon", { attrs: { "type": "attachment" } }), _vm._v(" " + _vm._s(f.name) + " ")], 1)]);
      }), 0)]) : _c("td", [_vm._v(" " + _vm._s(_vm.current.formdata[key]) + " ")])]);
    }), 0)]) : _c("div", { staticClass: "k-field-type-page-dialog-table" }, [_vm._v(" " + _vm._s(_vm.current.formdata.summary) + " ")]), _vm.current.error ? _c("k-box", { attrs: { "text": _vm.current.error, "theme": "negative" } }) : _vm._e()], 1);
  };
  var _sfc_staticRenderFns$3 = [];
  _sfc_render$3._withStripped = true;
  var __component__$3 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$3,
    _sfc_render$3,
    _sfc_staticRenderFns$3,
    false,
    null,
    null,
    null,
    null
  );
  __component__$3.options.__file = "/Users/romangsponer/Cloud/_sites/plugin-env/site/plugins/kirby-form-block-suite/src/components/dialog/Form.vue";
  const MailDialog = __component__$3.exports;
  const _sfc_main$2 = {
    props: {
      value: {
        type: Array,
        required: true
      },
      showuuid: Boolean,
      hideheader: Boolean
    },
    data() {
      return {
        isOpen: false
      };
    },
    computed: {
      items() {
        const a = this.value.content;
        if (a.length === 0) {
          return [
            {
              text: this.$t("form.block.inbox.empty"),
              theme: "disabled"
            }
          ];
        }
        return this.value.content;
      },
      headerText() {
        if (this.showuuid) {
          return this.value.header.name + " (" + this.value.uuid + ")";
        }
        return this.value.header.name;
      }
    },
    created() {
      this.isOpen = sessionStorage.getItem(
        `microman.form.showOpen.${this.value.page}.${this.value.uuid}`
      ) === "on";
    },
    methods: {
      toggleOpen() {
        this.isOpen = !this.isOpen;
        sessionStorage.setItem(
          `microman.form.showOpen.${this.value.page}.${this.value.uuid}`,
          this.isOpen ? "on" : "off"
        );
      }
    }
  };
  var _sfc_render$2 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "k-mailview-list" }, [!_vm.hideheader ? _c("k-box", { attrs: { "theme": _vm.value.header.state.theme, "icon": _vm.isOpen ? "angle-up" : "angle-down", "text": _vm.headerText }, nativeOn: { "click": function($event) {
      return _vm.toggleOpen();
    } } }) : _vm._e(), _vm.isOpen || _vm.hideheader ? _c("k-items", { attrs: { "items": _vm.items } }) : _vm._e()], 1);
  };
  var _sfc_staticRenderFns$2 = [];
  _sfc_render$2._withStripped = true;
  var __component__$2 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$2,
    _sfc_render$2,
    _sfc_staticRenderFns$2,
    false,
    null,
    null,
    null,
    null
  );
  __component__$2.options.__file = "/Users/romangsponer/Cloud/_sites/plugin-env/site/plugins/kirby-form-block-suite/src/components/MailList.vue";
  const MailList = __component__$2.exports;
  const _sfc_main$1 = {
    props: {
      value: {
        type: String,
        default: ""
      },
      dateformat: {
        type: String,
        default: "DD.MM.YYYY HH:mm"
      },
      forms: {
        type: Array,
        default: () => []
      },
      formData: {
        type: Object,
        default: () => {
        }
      },
      license: {
        type: String,
        default() {
          return "";
        }
      }
    },
    data() {
      return {
        data: [],
        filter: [],
        loading: true,
        hideheader: false
      };
    },
    computed: {
      thispage() {
        return this.$attrs.endpoints.model.replace("/pages/", "").replace(/\+/g, "/");
      }
    },
    created() {
      if (this.formData.formid) {
        this.filter = [this.formData.formid];
        this.hideheader = true;
      } else {
        this.filter = this.forms;
      }
      this.updateList();
      window.panel.events.on("form.update", this.updateList);
    },
    destroyed() {
      window.panel.events.off("form.update", this.updateList);
    },
    methods: {
      send(action, params, callback) {
        var _a, _b;
        this.$api.get("formblock", {
          action,
          page_id: this.thispage,
          request_id: (_a = params == null ? void 0 : params.request) != null ? _a : "",
          form_id: (_b = params == null ? void 0 : params.form) != null ? _b : "",
          params: JSON.stringify(params)
        }).then((data) => {
          this.loading = false;
          callback(data);
        });
      },
      isUnique(a) {
        return this.data.filter((b) => {
          return a.header.page === b.header.page && a.header.name === b.header.name;
        }).length > 1;
      },
      updateList() {
        let $this = this;
        this.send("requestsArray", { filter: this.filter }, (data) => {
          this.data = Object.keys(data).map(function(key) {
            data[key].content = data[key].content.map((req) => {
              req.formid = key;
              req.attachment = "attachment" in req ? JSON.parse(req.attachment) : false;
              req.formdata = JSON.parse(req.formdata);
              req.formfields = "formfields" in req ? JSON.parse(req.formfields) : false;
              let thisDate = $this.$library.dayjs(
                req.received,
                "YYYY-MM-DD HH:mm:ss"
              );
              req.info = thisDate.isValid() ? thisDate.format($this.dateformat) : "";
              req.text = $this.getLabel(req);
              req.image = $this.getImage(req);
              req.buttons = [$this.getButton("info", req)];
              req.options = [
                req.read === "" ? $this.getButton("unread", req) : $this.getButton("read", req),
                $this.getButton("delete", req)
              ];
              return req;
            });
            return data[key];
          });
        });
      },
      setRead(state, item) {
        this.send(
          "update",
          {
            form: item.formid,
            request: item.slug,
            read: state == false ? "" : this.$library.dayjs().format("YYYY-MM-DD HH:mm:ss")
          },
          () => {
            window.panel.events.emit("form.update");
            this.$panel.dialog.close();
          }
        );
      },
      getLabel(req) {
        if (req.display)
          return req.display;
        if (!this.value)
          return req.id;
        return this.$helper.string.template(this.value, req.formdata);
      },
      getButton(type, item) {
        if (type === "delete") {
          return {
            icon: "trash",
            text: this.$t("form.block.inbox.delete"),
            click: () => this.send(
              "delete",
              {
                form: item.formid,
                request: item.slug
              },
              () => {
                window.panel.events.emit("form.update");
              }
            )
          };
        }
        if (type === "unread") {
          return {
            icon: "preview",
            text: this.$t("form.block.inbox.asread"),
            click: () => this.setRead(true, item)
          };
        }
        if (type === "read") {
          return {
            icon: "hidden",
            text: this.$t("form.block.inbox.asunread"),
            click: () => this.setRead(false, item)
          };
        }
        return {
          icon: "info",
          click: () => this.$panel.dialog.open({
            component: "k-mail-dialog",
            props: {
              current: item,
              size: "medium",
              submitButton: item.read ? {} : this.getButton("unread", item),
              cancelButton: item.read ? this.getButton("read", item) : {}
            }
          })
        };
      },
      getImage(req) {
        const out = { back: "transparent" };
        if (req.read)
          return Object.assign(out, {
            icon: "circle",
            color: "info"
          });
        if (req.error)
          return Object.assign(out, {
            icon: "cancel",
            color: "negative"
          });
        return Object.assign(out, {
          icon: "circle-filled",
          color: "positive"
        });
      }
    }
  };
  var _sfc_render$1 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "k-field-type-mail-view" }, [_vm.loading ? _c("k-box", { attrs: { "theme": "info", "icon": "loader", "text": _vm.$t("form.block.inbox.loading") } }) : _c("k-grid", { attrs: { "variant": "fields" } }, [_vm.license.length > 0 ? _c("k-formblock-license", { staticStyle: { "--width": "1/1" }, attrs: { "text": _vm.license } }) : _vm._e(), _vm._l(_vm.data, function(group) {
      return _c("k-mail-list", { key: group.slug, staticClass: "k-table k-field-type-mail-table", staticStyle: { "--width": "1/1" }, attrs: { "hideheader": _vm.hideheader, "value": group, "showuuid": _vm.isUnique(group) }, on: { "setRead": _vm.setRead, "deleteMail": _vm.deleteMail } });
    }), _vm.data.length === 0 ? _c("k-box", { staticStyle: { "--width": "1/1" }, attrs: { "theme": "info", "text": _vm.$t("form.block.inbox.empty") } }) : _vm._e()], 2)], 1);
  };
  var _sfc_staticRenderFns$1 = [];
  _sfc_render$1._withStripped = true;
  var __component__$1 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$1,
    _sfc_render$1,
    _sfc_staticRenderFns$1,
    false,
    null,
    null,
    null,
    null
  );
  __component__$1.options.__file = "/Users/romangsponer/Cloud/_sites/plugin-env/site/plugins/kirby-form-block-suite/src/components/fields/MailView.vue";
  const MailView = __component__$1.exports;
  const FormLicense_vue_vue_type_style_index_0_lang = "";
  const _sfc_main = {
    props: {
      text: {
        type: String,
        default() {
          return "";
        }
      }
    },
    data() {
      return {
        state: "notice",
        msg: this.text
      };
    },
    methods: {
      dialog() {
        const $this = this;
        this.$dialog("formblock/register", {
          on: {
            success(t) {
              $this.msg = t.message;
              $this.state = "positive";
              $this.$panel.dialog.close();
            }
          }
        });
      }
    }
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _vm.msg.length > 0 ? _c("k-box", { staticClass: "k-formblock-license", attrs: { "theme": _vm.state } }, [_vm._v(" " + _vm._s(_vm.$t(_vm.msg)) + " "), _vm.state === "notice" ? _c("span", { attrs: { "href": "#" }, on: { "click": function($event) {
      return _vm.dialog();
    } } }, [_vm._v(_vm._s(_vm.$t("form.block.license.info.link")))]) : _vm._e()]) : _vm._e();
  };
  var _sfc_staticRenderFns = [];
  _sfc_render._withStripped = true;
  var __component__ = /* @__PURE__ */ normalizeComponent(
    _sfc_main,
    _sfc_render,
    _sfc_staticRenderFns,
    false,
    null,
    null,
    null,
    null
  );
  __component__.options.__file = "/Users/romangsponer/Cloud/_sites/plugin-env/site/plugins/kirby-form-block-suite/src/components/FormLicense.vue";
  const FormLicense = __component__.exports;
  window.panel.plugin("microman/formblock", {
    fields: {
      mailview: MailView
    },
    components: {
      "k-mail-list": MailList,
      "k-mail-dialog": MailDialog,
      "k-formblock-license": FormLicense
    },
    blocks: {
      form: Form
    }
  });
})();
