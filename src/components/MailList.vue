<!-- eslint-disable vue/no-v-html -->
<template>
    <div id='maillist'>
        <table  class="k-table k-field-type-mail-table"
                :data-noheader="showHeader"
                :aria-expanded="isOpen">
            <thead
                v-if="!value.header.hide"
                class="k-field-type-mail-table-header"
                :data-state="value.header.state.state"
                :aria-controls="`collapse${_uid}`"
                @click="toggleAccordion()">
                <tr>
                    <th class="k-field-type-mail-header"  data-mobile="">
                        <p>{{value.header.page}} - {{value.header.name}} ({{value.header.state.text}})
                            <span v-if="showuuid"> ({{value.uuid}})</span>
                            </p>
                        <k-icon type="angle-up" :style="(isOpen) ? '' : 'transform:rotate(180deg);'"/>
                    </th>
                    
                </tr>
            </thead>
            <tbody  
                v-show="showHeader"
                :id="`collapse${_uid}`"
                class="k-field-type-mail-table-body">

                <tr v-if="value.content.length == 0">
                    <td data-mobile="">
                        <k-item class="k-field-type-mail-list-item">
                            {{$t('form.block.inbox.empty')}}
                        </k-item>
                    </td>
                </tr>
        
                <tr 
                    v-for="mail in value.content"
                    :key="mail.id">

                    <td data-mobile="">
                    
                        <k-item 
                            class="k-field-type-mail-list-item"
                            :options="[
                                mail.read == '' ? {icon: 'preview', text: $t('form.block.inbox.asread'), click: () => $emit('setRead', true, mail)} :
                                {icon: 'unread', text: $t('form.block.inbox.asunread'), click: () => $emit('setRead', false, mail)},
                                {icon: 'trash', text: $t('form.block.inbox.delete'), click: () => $emit('deleteMail', mail)}
                            ]"
                            @click="$emit('open', mail)"
                        >
                            
                            <k-status-icon :status="mail.status" :tooltip="mail.tooltip"/>
                            <header class="k-item-content">
                                <slot>
                                    <h3 class="k-item-title">{{mail.title}}</h3>
                                    <p class="k-item-info" v-html="mail.desc" />
                                </slot>
                            </header>
                            
                        </k-item>

                    </td>
                </tr>
                <tr v-if="value.length == 0">
                    <td>
                        <k-item
                            class="k-field-type-page-list-item-empty"
                            :text="$t('form.block.inbox.empty')"
                            disabled="true"
                        />

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
        
<script>


export default {
    props: {
        value: {
            type: Array,
            required: true
        },
        showuuid: Boolean
    },
    data () {
        return {
            data: [],
            isOpen: (this.value.openaccordion == "true")
        }
    },
    computed: {

        prev () {
            return this.previewfields;
        },
        showHeader () {
            return (this.isOpen || this.value.header.hide);
        }

    },
    methods: {

        toggleAccordion () {
            this.isOpen = ! this.isOpen;
            this.$emit('setAccordion', this.value.id, this.isOpen);
            
        }

    }
};
</script>

<style lang="scss">

    .k-field-type-mail-view {
        padding:0;
    }

    .k-field-type-mail-table {
        margin-bottom:1em;
    }

    .k-field-type-mail-table[data-noheader] .k-field-type-mail-table tr:first-child .k-item {
        border-radius: 0;
    }

    .k-field-type-mail-table tr {
        .k-item {
            border-radius: 0;
            &:focus-within {
                box-shadow: none;
            }
        }

        &:last-child .k-item {
            border-bottom-right-radius: var(--rounded);
            border-bottom-left-radius: var(--rounded);
        }
        &:first-child .k-item {
            border-top-right-radius: var(--rounded);
            border-top-left-radius: var(--rounded);
        }
    }

    .k-field-type-mail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;

    }

    .k-field-type-mail-list-item-empty em {
        font-style: italic;
        color: var(--color-text-light)
    }


    .k-field-type-mail-table-header {
        th {
            background-color: var(--color-gray-300);
            border-color: var(--color-gray-300);
            cursor: pointer;
        }

         + .k-field-type-mail-table-body {
            tr:first-child .k-item {
                border-top-right-radius: 0;
                border-top-left-radius: 0;
            }
        }

        &[data-state=new] th {
            color:var(--color-white);
            background-color: var(--color-green);
            border-color: var(--color-green);
        }
        &[data-state=ok] th {
            color: var(--color-gray-600);
        }
        &[data-state=error] th {
            background-color: var(--color-red);
            border-color: var(--color-red);
            color:var(--color-white);
        }
    }



</style>