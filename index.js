(function() {
  "use strict";
  var render$4 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", [_c("k-grid", { staticStyle: { "gap": "0.25rem", "--columns": "12" } }, [_c("k-input", _vm._b({ staticStyle: { "--width": "1/3" }, attrs: { "type": "text" }, on: { "input": _vm.onInput }, model: { value: _vm.content.name, callback: function($$v) {
      _vm.$set(_vm.content, "name", $$v);
    }, expression: "content.name" } }, "k-input", _vm.field("name"), false)), _vm.loading ? _c("k-box", { staticStyle: { "--width": "2/3" }, attrs: { "theme": "info", "icon": "loader", "text": _vm.$t("form.block.inbox.loading") } }) : _c("k-box", { staticStyle: { "--width": "2/3" }, attrs: { "icon": "email", "theme": _vm.status.theme, "text": _vm.$t("form.block.inbox.show") + " (" + _vm.status.text + ")" }, nativeOn: { "click": function($event) {
      return _vm.open.apply(null, arguments);
    } } })], 1)], 1);
  };
  var staticRenderFns$4 = [];
  render$4._withStripped = true;
  function normalizeComponent(scriptExports, render2, staticRenderFns2, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render2) {
      options.render = render2;
      options.staticRenderFns = staticRenderFns2;
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
  const __vue2_script$4 = {
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
      this.$events.$off("form.update", this.updateCount);
    },
    created() {
      const $this = this;
      this.$store.subscribe(function(mutation) {
        if (mutation.type == "content/STATUS")
          $this.$events.emit("form.update");
      });
      this.content.formid = this.id;
      this.updateCount();
      this.$events.on("form.update", this.updateCount);
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
  const __cssModules$4 = {};
  var __component__$4 = /* @__PURE__ */ normalizeComponent(
    __vue2_script$4,
    render$4,
    staticRenderFns$4,
    false,
    __vue2_injectStyles$4,
    null,
    null,
    null
  );
  function __vue2_injectStyles$4(context) {
    for (let o in __cssModules$4) {
      this[o] = __cssModules$4[o];
    }
  }
  __component__$4.options.__file = "src/components/blocks/Form.vue";
  var Form = /* @__PURE__ */ function() {
    return __component__$4.exports;
  }();
  var render$3 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
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
  var staticRenderFns$3 = [];
  render$3._withStripped = true;
  var Form_vue_vue_type_style_index_0_lang = "";
  const __vue2_script$3 = {
    extends: "k-dialog",
    props: {
      current: {
        type: Object,
        default() {
        }
      }
    }
  };
  const __cssModules$3 = {};
  var __component__$3 = /* @__PURE__ */ normalizeComponent(
    __vue2_script$3,
    render$3,
    staticRenderFns$3,
    false,
    __vue2_injectStyles$3,
    null,
    null,
    null
  );
  function __vue2_injectStyles$3(context) {
    for (let o in __cssModules$3) {
      this[o] = __cssModules$3[o];
    }
  }
  __component__$3.options.__file = "src/components/dialog/Form.vue";
  var MailDialog = /* @__PURE__ */ function() {
    return __component__$3.exports;
  }();
  var render$2 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", { staticClass: "k-mailview-list" }, [!_vm.hideheader ? _c("k-box", { attrs: { "theme": _vm.value.header.state.theme, "icon": _vm.isOpen ? "angle-up" : "angle-down", "text": _vm.headerText }, nativeOn: { "click": function($event) {
      return _vm.toggleOpen();
    } } }) : _vm._e(), _vm.isOpen || _vm.hideheader ? _c("k-items", { attrs: { "items": _vm.items } }) : _vm._e()], 1);
  };
  var staticRenderFns$2 = [];
  render$2._withStripped = true;
  const __vue2_script$2 = {
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
  const __cssModules$2 = {};
  var __component__$2 = /* @__PURE__ */ normalizeComponent(
    __vue2_script$2,
    render$2,
    staticRenderFns$2,
    false,
    __vue2_injectStyles$2,
    null,
    null,
    null
  );
  function __vue2_injectStyles$2(context) {
    for (let o in __cssModules$2) {
      this[o] = __cssModules$2[o];
    }
  }
  __component__$2.options.__file = "src/components/MailList.vue";
  var MailList = /* @__PURE__ */ function() {
    return __component__$2.exports;
  }();
  var render$1 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", { staticClass: "k-field-type-mail-view" }, [_vm.loading ? _c("k-box", { attrs: { "theme": "info", "icon": "loader", "text": _vm.$t("form.block.inbox.loading") } }) : _c("k-grid", { attrs: { "variant": "fields" } }, [_vm.showLicense ? _c("k-formblock-license", { staticStyle: { "--width": "1/1" }, on: { "onSuccess": function($event) {
      _vm.showLicense = false;
    } } }) : _vm._e(), _vm._l(_vm.data, function(group) {
      return _c("k-mail-list", { key: group.slug, staticClass: "k-table k-field-type-mail-table", staticStyle: { "--width": "1/1" }, attrs: { "hideheader": _vm.hideheader, "value": group, "showuuid": _vm.isUnique(group) }, on: { "setRead": _vm.setRead, "deleteMail": _vm.deleteMail } });
    }), _vm.data.length === 0 ? _c("k-box", { staticStyle: { "--width": "1/1" }, attrs: { "theme": "info", "text": _vm.$t("form.block.inbox.empty") } }) : _vm._e()], 2)], 1);
  };
  var staticRenderFns$1 = [];
  render$1._withStripped = true;
  const __vue2_script$1 = {
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
      license: Boolean
    },
    data() {
      return {
        data: [],
        filter: [],
        loading: true,
        showLicense: true,
        hideheader: false
      };
    },
    computed: {
      thispage() {
        return this.$attrs.endpoints.model.replace("/pages/", "").replace(/\+/g, "/");
      }
    },
    created() {
      this.showLicense = this.license;
      if (this.formData.formid) {
        this.filter = [this.formData.formid];
        this.hideheader = true;
      } else {
        this.filter = this.forms;
      }
      this.updateList();
      this.$events.on("form.update", this.updateList);
    },
    destroyed() {
      this.$events.off("form.update", this.updateList);
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
            this.$events.emit("form.update");
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
                this.$events.emit("form.update");
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
  const __cssModules$1 = {};
  var __component__$1 = /* @__PURE__ */ normalizeComponent(
    __vue2_script$1,
    render$1,
    staticRenderFns$1,
    false,
    __vue2_injectStyles$1,
    null,
    null,
    null
  );
  function __vue2_injectStyles$1(context) {
    for (let o in __cssModules$1) {
      this[o] = __cssModules$1[o];
    }
  }
  __component__$1.options.__file = "src/components/fields/MailView.vue";
  var MailView = /* @__PURE__ */ function() {
    return __component__$1.exports;
  }();
  var render = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", { staticClass: "k-formblock-license" }, [_c("k-box", { attrs: { "theme": "notice" } }, [_vm._v(" This is an unregistered version of the kirby form block suite. "), _vm.doRegister ? _c("span", { staticClass: "link", on: { "click": function($event) {
      return _vm.$refs.regdialog.open();
    } } }, [_vm._v("Register now")]) : _vm._e()]), _c("k-dialog", { ref: "regdialog", staticClass: "k-formblock-license-dialog", attrs: { "size": "medium" } }, [_c("k-grid", { attrs: { "gutter": "medium" } }, [_c("k-column", [_c("k-text", { attrs: { "size": "large" } }, [_vm._v(" Get your license "), _c("a", { attrs: { "href": "https://license.microman.ch/?product=801346", "target": "_blank" } }, [_vm._v("here")])])], 1), _c("k-column", [_c("k-text-field", { attrs: { "label": "Please enter your license code", "help": _vm.supporttext(), "required": "true", "placeholder": "" }, model: { value: _vm.licensekey, callback: function($$v) {
      _vm.licensekey = $$v;
    }, expression: "licensekey" } })], 1), _c("k-column", [_c("k-text-field", { attrs: { "label": "Email", "type": "text", "required": "true", "placeholder": "mail@example.com" }, model: { value: _vm.email, callback: function($$v) {
      _vm.email = $$v;
    }, expression: "email" } })], 1), _c("k-column", [_vm.theme ? _c("k-box", { staticClass: "loader-box", attrs: { "theme": _vm.theme } }, [_vm.theme === "notice" ? _c("k-loader") : _vm._e(), _c("span", { staticClass: "loader-text" }, [_vm._v(_vm._s(_vm.notify))])], 1) : _vm._e()], 1)], 1), _c("template", { slot: "footer" }, [_c("k-button-group", [_c("k-button", { attrs: { "icon": "chancel" }, on: { "click": _vm.reset } }, [_vm._v("Close")]), _c("k-button", { attrs: { "disabled": _vm.onLoad || _vm.onSuccess, "icon": "check", "theme": "positive" }, on: { "click": _vm.register } }, [_vm._v("Register")])], 1)], 1)], 2)], 1);
  };
  var staticRenderFns = [];
  render._withStripped = true;
  var FormLicense_vue_vue_type_style_index_0_lang = "";
  const __vue2_script = {
    props: {
      message: {
        type: String,
        default() {
          return "";
        }
      },
      supportLink: {
        type: String,
        default() {
          return "";
        }
      },
      isError: Boolean,
      doRegister: {
        type: Boolean,
        default() {
          return true;
        }
      },
      doSupport: {
        type: Boolean,
        default() {
          return true;
        }
      }
    },
    data() {
      return {
        onLoad: false,
        onError: false,
        onSuccess: false,
        licensekey: "",
        email: "",
        notify: ""
      };
    },
    computed: {
      theme() {
        if (this.onError) {
          return "negative";
        }
        if (this.onSuccess) {
          return "positive";
        }
        if (this.onLoad) {
          return "notice";
        }
        return false;
      }
    },
    methods: {
      supporttext() {
        return "Keep in mind: The domain of this Kirby instance will be linked to the license. If the license is already assigned by mistake, <a href='https://microman.ch/en/microman' target='_blank'>contact the support</a>";
      },
      reset() {
        this.licensekey = this.email = this.notify = "";
        this.onError = this.onLoad = false;
        if (this.onSuccess) {
          this.$emit("onSuccess");
        }
        this.$refs.regdialog.close();
      },
      async register() {
        this.onError = this.onSuccess = false;
        this.onLoad = true;
        this.notify = "Checking your license code. Please wait...";
        this.$api.get("formblock/license", {
          key: this.licensekey,
          email: this.email
        }).then((data) => {
          this.onLoad = false;
          this.onError = data.error;
          this.onSuccess = data.success;
          this.notify = data.text;
        });
      }
    }
  };
  const __cssModules = {};
  var __component__ = /* @__PURE__ */ normalizeComponent(
    __vue2_script,
    render,
    staticRenderFns,
    false,
    __vue2_injectStyles,
    null,
    null,
    null
  );
  function __vue2_injectStyles(context) {
    for (let o in __cssModules) {
      this[o] = __cssModules[o];
    }
  }
  __component__.options.__file = "src/components/FormLicense.vue";
  var FormLicense = /* @__PURE__ */ function() {
    return __component__.exports;
  }();
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
