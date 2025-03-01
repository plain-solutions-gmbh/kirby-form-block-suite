<template>
  <div>
    <k-grid style="gap: 0.25rem; --columns: 12">
      <k-input
        v-model="content.name"
        v-bind="field('name')"
        style="--width: 1/3"
        type="text"
        @input="onInput"
      />

      <k-box
        v-if="loading"
        style="--width: 2/3"
        theme="info"
        icon="loader"
        :text="$t('form.block.inbox.loading')"
      />

      <k-box
        v-else
        icon="email"
        style="--width: 2/3"
        :theme="status.theme"
        :text="$t('form.block.inbox.show') + ' (' + status.text + ')'"
        @click.native="open"
      />
    </k-grid>
  </div>
</template>

<script>
export default {
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
          state: "wait",
        },
      },
    };
  },
  destroyed() {
    window.panel.events.off("form.update", this.updateCount);
  },
  created() {
    window.panel.events.on("content/STATUS", function (mutation) {
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
      this.$api
        .get("formblock", {
          action: "info",
          form_id: this.id,
          params: JSON.stringify({ form_name: this.content.name }),
        })
        .then((data) => {
          $this.status = data;
          this.loading = false;
        });
    },
    onInput(value) {
      this.$emit("update", value);
    },
  },
};
</script>
