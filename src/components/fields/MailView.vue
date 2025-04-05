<template>
  <div class="k-field-type-mail-view">
    <k-box
      v-if="loading"
      theme="info"
      icon="loader"
      :text="$t('form.block.inbox.loading')"
    />

    <k-grid v-else variant="fields" :style="{ gap: 'var(--spacing-2)' }">
      <k-mail-list
        v-for="group in data"
        :key="group.slug"
        class="k-table k-field-type-mail-table"
        :hideheader="hideheader"
        style="--width: 1/1"
        :value="group"
        :showuuid="isUnique(group)"
        @setRead="setRead"
        @deleteMail="deleteMail"
      />

      <k-box
        v-if="data.length === 0"
        style="--width: 1/1"
        theme="info"
        :text="$t('form.block.inbox.empty')"
      />
    </k-grid>
    <k-plain-license prefix="formblock" />
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: String,
      default: "",
    },
    dateformat: {
      type: String,
      default: "DD.MM.YYYY HH:mm",
    },
    forms: {
      type: Array,
      default: () => [],
    },
    formData: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      data: [],
      filter: [],
      loading: true,
      hideheader: false,
    };
  },
  computed: {
    thispage() {
      return this.$attrs.endpoints.model
        .replace("/pages/", "")
        .replace(/\+/g, "/");
    },
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
      this.$api
        .get("formblock", {
          action: action,
          page_id: this.thispage,
          request_id: params?.request ?? "",
          form_id: params?.form ?? "",
          params: JSON.stringify(params),
        })
        .then((data) => {
          this.loading = false;
          callback(data);
        });
    },
    isUnique(a) {
      return (
        this.data.filter((b) => {
          return (
            a.header.page === b.header.page && a.header.name === b.header.name
          );
        }).length > 1
      );
    },
    updateList() {
      let $this = this;
      this.send("requestsArray", { filter: this.filter }, (data) => {
        this.data = Object.keys(data).map(function (key) {
          data[key].content = data[key].content.map((req) => {
            req.formid = key;
            req.attachment =
              "attachment" in req ? JSON.parse(req.attachment) : false;
            req.formdata = JSON.parse(req.formdata);
            req.formfields =
              "formfields" in req ? JSON.parse(req.formfields) : false;
            let thisDate = $this.$library.dayjs(
              req.received,
              "YYYY-MM-DD HH:mm:ss"
            );
            req.info = thisDate.isValid()
              ? thisDate.format($this.dateformat)
              : "";
            req.text = $this.getLabel(req);
            req.image = $this.getImage(req);
            req.buttons = [$this.getButton("info", req)];
            req.options = [
              req.read === ""
                ? $this.getButton("unread", req)
                : $this.getButton("read", req),
              $this.getButton("delete", req),
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
          read:
            state == false
              ? ""
              : this.$library.dayjs().format("YYYY-MM-DD HH:mm:ss"),
        },
        () => {
          window.panel.events.emit("form.update");
          this.$panel.dialog.close();
        }
      );
    },
    getLabel(req) {
      if (req.display) return req.display;

      if (!this.value) return req.id;

      return this.$helper.string.template(this.value, req.formdata);
    },
    getButton(type, item) {
      if (type === "delete") {
        return {
          icon: "trash",
          text: this.$t("form.block.inbox.delete"),
          click: () =>
            this.send(
              "delete",
              {
                form: item.formid,
                request: item.slug,
              },
              () => {
                window.panel.events.emit("form.update");
              }
            ),
        };
      }

      if (type === "unread") {
        return {
          icon: "preview",
          text: this.$t("form.block.inbox.asread"),
          click: () => this.setRead(true, item),
        };
      }

      if (type === "read") {
        return {
          icon: "hidden",
          text: this.$t("form.block.inbox.asunread"),
          click: () => this.setRead(false, item),
        };
      }

      return {
        icon: "info",
        click: () =>
          this.$panel.dialog.open({
            component: "k-mail-dialog",
            props: {
              current: item,
              size: "medium",
              submitButton: item.read ? {} : this.getButton("unread", item),
              cancelButton: item.read ? this.getButton("read", item) : {},
            },
          }),
      };
    },
    getImage(req) {
      //Readed
      if (req.read)
        return {
          icon: "circle",
          color: "yellow",
          back: "transparent",
        };

      //Error
      if (req.error)
        return {
          icon: "cancel",
          color: "red",
          back: "transparent",
        };

      //New
      return {
        icon: "circle-filled",
        color: "green",
        back: "transparent",
      };
    },
  },
};
</script>
