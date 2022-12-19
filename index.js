(function() {
  "use strict";
  var render$2 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", { staticClass: "k-block-type-form" }, [_c("div", { staticClass: "k-block-type-form-wrapper", attrs: { "data-state": _vm.state }, on: { "click": _vm.open } }, [_c("k-input", { attrs: { "name": "name", "type": "text" }, on: { "input": _vm.onInput }, model: { value: _vm.content.name, callback: function($$v) {
      _vm.$set(_vm.content, "name", $$v);
    }, expression: "content.name" } }), _c("k-tag", { attrs: { "data-state": _vm.status.state } }, [_vm._v(_vm._s(_vm.$t("form.block.inbox.show")) + " (" + _vm._s(_vm.status.text) + ")")])], 1)]);
  };
  var staticRenderFns$2 = [];
  render$2._withStripped = true;
  var Form_vue_vue_type_style_index_0_lang = "";
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
  const __vue2_script$2 = {
    data() {
      return {
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
    computed: {
      thisPage() {
        return this.$attrs.endpoints.model.replace("/pages/", "").replace(/\+/g, "/");
      }
    },
    destroyed() {
      this.$events.$off("form.update", this.updateCount);
    },
    created() {
      const $this = this;
      this.$store.subscribe(function(mutation) {
        if (mutation.type == "content/STATUS")
          $this.$events.$emit("form.update");
      });
      this.updateCount();
      this.$events.$on("form.update", this.updateCount);
    },
    methods: {
      updateCount() {
        const $this = this;
        this.$api.get("form/get-requests-count", { form: this.thisPage + "/" + this.$attrs.id, name: this.content.name }).then((data) => $this.status = data).catch(
          function() {
            $this.error = $this.$t("form.block.inbox.error");
          }
        );
      },
      confirmToRemove() {
        this.$refs.removeDialog.open();
      },
      onInput(value) {
        this.$emit("update", value);
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
  __component__$2.options.__file = "src/components/blocks/Form.vue";
  var Form = /* @__PURE__ */ function() {
    return __component__$2.exports;
  }();
  var render$1 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", { attrs: { "id": "maillist" } }, [_c("table", { staticClass: "k-table k-field-type-mail-table", attrs: { "data-noheader": _vm.showHeader, "aria-expanded": _vm.isOpen } }, [!_vm.value.header.hide ? _c("thead", { staticClass: "k-field-type-mail-table-header", attrs: { "data-state": _vm.value.header.state.state, "aria-controls": "collapse" + _vm._uid }, on: { "click": function($event) {
      return _vm.toggleAccordion();
    } } }, [_c("tr", [_c("th", { staticClass: "k-field-type-mail-header", attrs: { "data-mobile": "" } }, [_c("p", [_vm._v(_vm._s(_vm.value.header.page) + " - " + _vm._s(_vm.value.header.name) + " (" + _vm._s(_vm.value.header.state.text) + ") "), _vm.showuuid ? _c("span", [_vm._v(" (" + _vm._s(_vm.value.uuid) + ")")]) : _vm._e()]), _c("k-icon", { style: _vm.isOpen ? "" : "transform:rotate(180deg);", attrs: { "type": "angle-up" } })], 1)])]) : _vm._e(), _c("tbody", { directives: [{ name: "show", rawName: "v-show", value: _vm.showHeader, expression: "showHeader" }], staticClass: "k-field-type-mail-table-body", attrs: { "id": "collapse" + _vm._uid } }, [_vm.value.content.length == 0 ? _c("tr", [_c("td", { attrs: { "data-mobile": "" } }, [_c("k-item", { staticClass: "k-field-type-mail-list-item" }, [_vm._v(" " + _vm._s(_vm.$t("form.block.inbox.empty")) + " ")])], 1)]) : _vm._e(), _vm._l(_vm.value.content, function(mail) {
      return _c("tr", { key: mail.id }, [_c("td", { attrs: { "data-mobile": "" } }, [_c("k-item", { staticClass: "k-field-type-mail-list-item", attrs: { "options": [
        mail.read == "" ? { icon: "preview", text: _vm.$t("form.block.inbox.asread"), click: function() {
          return _vm.$emit("setRead", true, mail);
        } } : { icon: "unread", text: _vm.$t("form.block.inbox.asunread"), click: function() {
          return _vm.$emit("setRead", false, mail);
        } },
        { icon: "trash", text: _vm.$t("form.block.inbox.delete"), click: function() {
          return _vm.$emit("deleteMail", mail);
        } }
      ] }, on: { "click": function($event) {
        return _vm.$emit("open", mail);
      } } }, [_c("k-status-icon", { attrs: { "status": mail.status, "tooltip": mail.tooltip } }), _c("header", { staticClass: "k-item-content" }, [_vm._t("default", function() {
        return [_c("h3", { staticClass: "k-item-title" }, [_vm._v(_vm._s(mail.title))]), _c("p", { staticClass: "k-item-info", domProps: { "innerHTML": _vm._s(mail.desc) } })];
      })], 2)], 1)], 1)]);
    }), _vm.value.length == 0 ? _c("tr", [_c("td", [_c("k-item", { staticClass: "k-field-type-page-list-item-empty", attrs: { "text": _vm.$t("form.block.inbox.empty"), "disabled": "true" } })], 1)]) : _vm._e()], 2)])]);
  };
  var staticRenderFns$1 = [];
  render$1._withStripped = true;
  var MailList_vue_vue_type_style_index_0_lang = "";
  const __vue2_script$1 = {
    props: {
      value: {
        type: Array,
        required: true
      },
      showuuid: Boolean
    },
    data() {
      return {
        data: [],
        isOpen: this.value.openaccordion == "true"
      };
    },
    computed: {
      prev() {
        return this.previewfields;
      },
      showHeader() {
        return this.isOpen || this.value.header.hide;
      }
    },
    methods: {
      toggleAccordion() {
        this.isOpen = !this.isOpen;
        this.$emit("setAccordion", this.value.id, this.isOpen);
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
  __component__$1.options.__file = "src/components/MailList.vue";
  var MailList = /* @__PURE__ */ function() {
    return __component__$1.exports;
  }();
  var render = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", { staticClass: "k-field-type-mail-view" }, [_vm.data ? _vm._l(_vm.data, function(group) {
      return _c("k-mail-list", { key: group.slug, staticClass: "k-table k-field-type-mail-table", attrs: { "value": group, "showuuid": !_vm.isUnique }, on: { "open": _vm.openMail, "setRead": _vm.setRead, "deleteMail": _vm.deleteMail, "setAccordion": _vm.setAccordion } });
    }) : _vm._e(), _vm.loading ? _c("k-info-field", { attrs: { "text": _vm.$t("form.block.inbox.loading") } }) : _vm._e(), _c("k-dialog", { ref: "dialog", staticClass: "k-field-type-page-dialog", attrs: { "size": "large" } }, [_c("k-headline", [_vm._v(_vm._s(_vm.current.title))]), _vm.current.formfields ? _c("div", [_c("table", { staticClass: "k-field-type-page-dialog-table" }, _vm._l(_vm.current.formfields, function(label, key) {
      return _c("tr", { key, class: "field_" + key }, [_c("td", [_vm._v(_vm._s(label))]), _vm.current.attachment[key] ? _c("td", [_c("ul", { staticClass: "k-field-type-page-dialog-linklist" }, _vm._l(_vm.current.attachment[key], function(f) {
        return _c("li", { key: f.tmp_name }, [_c("a", { staticClass: "k-field-type-page-dialog-link", attrs: { "href": f.location, "download": f.name } }, [_c("k-icon", { attrs: { "type": "attachment" } }), _vm._v(" " + _vm._s(f.name) + " ")], 1)]);
      }), 0)]) : _c("td", [_vm._v(" " + _vm._s(_vm.current.formdata[key]) + " ")])]);
    }), 0)]) : _c("div", { staticClass: "k-field-type-page-dialog-table", domProps: { "innerHTML": _vm._s(_vm.current.formdata.summary) } }), _vm.current.length > 0 ? _c("k-fieldset", { attrs: { "disabled": "true", "fields": _vm.prev }, model: { value: _vm.current, callback: function($$v) {
      _vm.current = $$v;
    }, expression: "current" } }) : _vm._e(), _vm.current.error ? _c("k-info-field", { attrs: { "text": _vm.current.error, "theme": "negative" } }) : _vm._e(), _c("template", { slot: "footer" }, [_c("k-button-group", [_vm.current.read != "" ? _c("k-button", { on: { "click": function($event) {
      return _vm.setRead(false);
    } } }, [_vm._v(_vm._s(_vm.$t("form.block.inbox.asunread")))]) : _vm._e(), _c("k-button", { attrs: { "icon": "cancel" }, on: { "click": function($event) {
      return _vm.$refs.dialog.close();
    } } }, [_vm._v(_vm._s(_vm.$t("close")))]), _vm.current.read == "" ? _c("k-button", { on: { "click": function($event) {
      return _vm.setRead(true);
    } } }, [_vm._v(_vm._s(_vm.$t("form.block.inbox.asread")))]) : _vm._e()], 1)], 1)], 2)], 2);
  };
  var staticRenderFns = [];
  render._withStripped = true;
  var MailView_vue_vue_type_style_index_0_lang = "";
  const __vue2_script = {
    props: {
      value: {
        type: String,
        default: ""
      },
      dateformat: {
        type: String,
        default: "DD.MM.YYYY HH:mm"
      }
    },
    data() {
      return {
        new: [],
        read: [],
        data: [],
        current: {
          formdata: {},
          formfields: {},
          attachment: {}
        },
        id: 0,
        parent: false,
        loading: true,
        page: "Keine Seite"
      };
    },
    computed: {
      prev() {
        return this.previewfields;
      },
      isUnique() {
        let uniqueTest = [];
        let isUnique = true;
        this.data.forEach((element) => {
          if (uniqueTest.includes(element.header.name))
            isUnique = false;
          uniqueTest.push(element.header.name);
        });
        return isUnique;
      },
      thisPage() {
        return this.$attrs.endpoints.model.replace("/pages/", "").replace(/\+/g, "/");
      }
    },
    created() {
      this.findId(this.$parent);
      this.$events.$on("form.update", this.updateList);
    },
    destroyed() {
      this.$events.$off("form.update", this.updateList);
    },
    methods: {
      findId(parent) {
        var _a, _b, _c, _d;
        if (parent) {
          this.parent = (_d = (_c = (_b = (_a = parent.$parent) == null ? void 0 : _a.$options) == null ? void 0 : _b.propsData) == null ? void 0 : _c.id) != null ? _d : false;
        } else {
          this.parent = "";
        }
        if (typeof this.parent == "string") {
          this.updateList();
          return;
        }
        this.findId(parent.$parent);
      },
      updateList() {
        let $this = this;
        this.$api.get("form/get-requests", { page: this.thisPage, form: this.parent ? this.parent : "" }).then((data) => {
          this.data = Object.keys(data).map(function(key) {
            data[key].content = data[key].content.map((req) => {
              req.attachment = "attachment" in req ? JSON.parse(req.attachment) : false;
              req.formdata = JSON.parse(req.formdata);
              req.formfields = "formfields" in req ? JSON.parse(req.formfields) : false;
              req.status = $this.getStatus(req);
              req.tooltip = $this.getTooltip(req);
              let thisDate = $this.$library.dayjs(req.received, "YYYY-MM-DD HH:mm:ss");
              req.desc = thisDate.isValid() ? thisDate.format($this.dateformat) : "";
              req.title = $this.getLabel(req);
              return req;
            });
            return data[key];
          });
          this.loading = false;
        });
      },
      openMail(request) {
        this.current = request;
        this.$refs.dialog.open();
      },
      setRead(state, request = false) {
        if (!request)
          request = this.current;
        this.$api.get("form/set-read", { form: request.parent, request: request.slug, state }).then((data) => {
          if (data) {
            this.$events.$emit("form.update");
            this.$refs.dialog.close();
          }
        });
      },
      setAccordion(form, value) {
        this.$api.get("form/setAccodion", { form, value }).then(() => {
          this.$events.$emit("form.update");
        });
      },
      deleteMail(request) {
        this.$api.get("form/delete-request", { form: request.parent, request: request.slug }).then(() => {
          this.$events.$emit("form.update");
        });
      },
      getLabel(req) {
        if (req.display)
          return req.display;
        if (!this.value)
          return req.id;
        return this.$helper.string.template(this.value, req.formdata);
      },
      getStatus(req) {
        if (req.read)
          return "unlisted";
        if (req.error)
          return "draft";
        return "listed";
      },
      getTooltip(req) {
        if (req.error != "")
          return req.error;
        if (req.read != "")
          return this.$t("form.block.inbox.tooltip.read");
        return this.$t("form.block.inbox.tooltip.unread");
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
  __component__.options.__file = "src/components/fields/MailView.vue";
  var MailView = /* @__PURE__ */ function() {
    return __component__.exports;
  }();
  window.panel.plugin("microman/formblock", {
    fields: {
      mailview: MailView
    },
    components: {
      "k-mail-list": MailList
    },
    blocks: {
      form: Form
    },
    icons: {
      form: '<path d="M6.9,13.6H2.2c-0.6,0-1.1-0.5-1.1-1.1V3.1C1.1,2.5,1.6,2,2.2,2h8.4c0.6,0,1.1,0.5,1.1,1.1v5.8 c0,0.3,0.2,0.5,0.5,0.5s0.5-0.2,0.5-0.5V3.1c0-1.2-0.9-2.1-2.1-2.1H2.2C1,1,0.1,1.9,0.1,3.1v9.5c0,1.2,0.9,2.1,2.1,2.1h4.7 c0.3,0,0.5-0.2,0.5-0.5C7.5,13.8,7.2,13.6,6.9,13.6z M9,4.1H3.8c-0.3,0-0.5,0.2-0.5,0.5c0,0.3,0.2,0.5,0.5,0.5H9 c0.3,0,0.5-0.2,0.5-0.5C9.6,4.4,9.3,4.1,9,4.1z M9.6,7.8c0-0.3-0.2-0.5-0.5-0.5H3.8c-0.3,0-0.5,0.2-0.5,0.5c0,0.3,0.2,0.5,0.5,0.5H9 C9.3,8.3,9.6,8.1,9.6,7.8z M3.8,10.4c-0.3,0-0.5,0.2-0.5,0.5c0,0.3,0.2,0.5,0.5,0.5h2.1c0.3,0,0.5-0.2,0.5-0.5 c0-0.3-0.2-0.5-0.5-0.5H3.8z M15.8,9.5c-0.2-0.2-0.5-0.2-0.7,0l-3.9,3.9l-1.8-1.8c-0.2-0.2-0.5-0.2-0.7,0c-0.2,0.2-0.2,0.5,0,0.7 l2,2c0,0.1,0.1,0.1,0.1,0.1c0.2,0.2,0.5,0.2,0.7,0l4.3-4.3C16,10,16,9.7,15.8,9.5z"/>',
      send: '<path d="M15.8,0.7C15.8,0.7,15.8,0.7,15.8,0.7C15.8,0.7,15.8,0.6,15.8,0.7c0-0.1,0-0.1,0-0.1c0,0,0-0.1,0-0.1c0,0,0,0,0,0 c0,0,0,0,0-0.1c0,0,0,0,0,0c0,0,0-0.1-0.1-0.1c0,0,0,0-0.1-0.1c0,0,0,0,0,0c0,0,0,0-0.1,0c0,0,0,0,0,0c0,0-0.1,0-0.1,0c0,0,0,0,0,0 c0,0,0,0-0.1,0c0,0,0,0,0,0c0,0,0,0-0.1,0c0,0,0,0,0,0c0,0-0.1,0-0.1,0L0.5,5.7C0.3,5.8,0.2,5.9,0.2,6.2c0,0.2,0.1,0.4,0.3,0.5 l6,2.9l2.9,6c0.1,0.2,0.3,0.3,0.5,0.3c0,0,0,0,0,0c0.2,0,0.4-0.1,0.5-0.3l5.5-14.6C15.8,0.8,15.8,0.8,15.8,0.7 C15.8,0.8,15.8,0.8,15.8,0.7C15.8,0.8,15.8,0.7,15.8,0.7z M13.2,2L6.7,8.5L2,6.2L13.2,2z M9.8,14L7.5,9.3L14,2.8L9.8,14z"/>',
      unread: '<path d="M15.72,8.44c0-0.26-0.11-0.52-0.3-0.71c-0.37-0.37-1.04-0.37-1.41,0c-0.08,0.08-0.13,0.18-0.18,0.28L13.82,8 c-0.78,1.17-2.98,4-5.82,4c-2.83,0-5.02-2.82-5.81-3.99c-0.01,0-0.01,0-0.02,0c-0.05-0.1-0.11-0.2-0.19-0.28 c-0.37-0.37-1.04-0.37-1.41,0C0.38,7.92,0.28,8.18,0.28,8.44c0,0.27,0.1,0.52,0.29,0.71c0,0.01-0.01,0.01-0.01,0.02 C1.55,10.64,4.23,14,8,14c3.92,0,6.68-3.66,7.56-5.01l-0.02-0.01C15.65,8.82,15.72,8.64,15.72,8.44z"/>'
    }
  });
})();
