<template>
  <k-dialog
    ref="dialog"
    class="k-field-type-page-dialog"
    v-bind="$props"
    @cancel="$emit('cancel')"
    @submit="$emit('submit')"
  >
    <k-headline>{{ current.title }}</k-headline>

    <div v-if="current.formfields">
      <table class="k-field-type-page-dialog-table">
        <tr
          v-for="(label, key) in current.formfields"
          :key="key"
          :class="'field_' + key"
        >
          <td>{{ label }}</td>
          <td v-if="current.attachment[key]">
            <ul class="k-field-type-page-dialog-linklist">
              <li v-for="f in current.attachment[key]" :key="f.tmp_name">
                <a
                  class="k-field-type-page-dialog-link"
                  :href="f.location"
                  :download="f.name"
                >
                  <k-icon type="attachment" />
                  {{ f.name }}
                </a>
              </li>
            </ul>
          </td>
          <td v-else>
            {{ current.formdata[key] }}
          </td>
        </tr>
      </table>
    </div>

    <div v-else class="k-field-type-page-dialog-table">
      {{ current.formdata.summary }}
    </div>

    <k-box v-if="current.error" :text="current.error" theme="negative" />
  </k-dialog>
</template>

<script>
export default {
  extends: "k-dialog",
  props: {
    current: {
      type: Object,
      default() {},
    },
  },
};
</script>

<style lang="scss">
.k-field-type-page-dialog-table {
  width: 100%;
  background: var(--item-color-back, white);
  padding: var(--spacing-3);

  td,
  th {
    vertical-align: top;
    padding: var(--spacing-2);
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .field_summary {
    display: none;
  }
  .k-field-type-page-change-display {
    padding-top: 3px;
  }
  .k-field-type-page-dialog-link {
    display: flex;
    font-size: 0.9em;
    line-height: 1.75em;
    span.k-icon {
      --size: 0.99em;
      margin-right: 6px;
    }
  }
}
</style>
