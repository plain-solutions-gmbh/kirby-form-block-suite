<!-- eslint-disable vue/no-v-html -->
<template>
  <div class="k-mailview-list">
    <k-box
      v-if="!hideheader"
      :theme="value.header.state.theme"
      :icon="isOpen ? 'angle-up' : 'angle-down'"
      :text="headerText"
      @click.native="toggleOpen()"
    />

    <k-items v-if="isOpen || hideheader" :items="items" />
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: Array,
      required: true,
    },
    showuuid: Boolean,
    hideheader: Boolean,
  },
  data() {
    return {
      isOpen: false,
    };
  },
  computed: {
    items() {
      const a = this.value.content;

      if (a.length === 0) {
        return [
          {
            text: this.$t("form.block.inbox.empty"),
            theme: "disabled",
          },
        ];
      }

      return this.value.content;
    },
    headerText() {
      if (this.showuuid) {
        return this.value.header.name + " (" + this.value.uuid + ")";
      }
      return this.value.header.name;
    },
  },
  created() {
    this.isOpen =
      sessionStorage.getItem(
        `plain.form.showOpen.${this.value.page}.${this.value.uuid}`
      ) === "on";
  },
  methods: {
    toggleOpen() {
      this.isOpen = !this.isOpen;
      sessionStorage.setItem(
        `plain.form.showOpen.${this.value.page}.${this.value.uuid}`,
        this.isOpen ? "on" : "off"
      );
    },
  },
};
</script>
