<template>
    <div class="k-field-type-mail-view">
        <!-- eslint-disable vue/no-v-html -->

        <template v-if="data">
            <k-mail-list
                v-for="group in data"
                :key="group.slug"
                class="k-table k-field-type-mail-table"
                :value='group'
                :showuuid="!isUnique"
                @open="openMail"
                @setRead="setRead"
                @deleteMail="deleteMail"
                @setAccordion="setAccordion" />
        </template>

        <k-info-field v-if="loading" :text="$t('form.block.inbox.loading')" />

        <k-dialog ref="dialog" class="k-field-type-page-dialog" size="large">
        
            <k-headline>{{current.title}}</k-headline>

            <div v-if="current.formfields">

                <table class="k-field-type-page-dialog-table">
                    <tr 
                        v-for="(label, key) in current.formfields"
                        :key="key"
                        :class="'field_'+key"
                        >
                            <td>{{label}}</td>
                            <td v-if="current.attachment[key]">
                                <ul class="k-field-type-page-dialog-linklist" >
                                    <li
                                        v-for="f in current.attachment[key]"
                                        :key="f.tmp_name">
                                        <a 
                                            class="k-field-type-page-dialog-link"
                                            :href="f.location"
                                            :download="f.name">
                                            <k-icon type="attachment" />
                                            {{f.name}}
                                        </a>
                                    </li>
                                </ul>
                            </td>
                            <td v-else>
                                {{current.formdata[key]}}
                            </td>
                    </tr>
                </table>
            </div>

            <div v-else class="k-field-type-page-dialog-table" v-html="current.formdata.summary" />

            <k-fieldset v-if="current.length > 0" v-model="current" disabled="true" :fields="prev" />            

            <k-info-field v-if="current.error" :text="current.error" theme="negative" />

            <template slot="footer">
                <k-button-group>
                <k-button v-if="current.read != ''" @click="setRead(false)">{{$t('form.block.inbox.asunread')}}</k-button>
                <k-button icon="cancel" @click="$refs.dialog.close()">{{$t('close')}}</k-button>
                <k-button v-if="current.read == ''" @click="setRead(true)">{{$t('form.block.inbox.asread')}}</k-button>
                </k-button-group>
            </template>
            
        </k-dialog>
    </div>
    

</template>

<script>


export default {
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
    data () {
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
            parent:false,
            loading: true,
            page: "Keine Seite"
        };
    },
    computed: {
        prev () {

            return this.previewfields;

        },
        isUnique () {

            let uniqueTest = [];
            let isUnique = true;

            this.data.forEach(element => {
                if (uniqueTest.includes(element.header.name))
                    isUnique = false;
                uniqueTest.push(element.header.name);
            });
            return isUnique;

        },
        thisPage () {

            return this.$attrs.endpoints.model.replace('/pages/', '').replace(/\+/g, '/');

        },
    },
    created () {

        this.findId(this.$parent);
        this.$events.$on("form.update", this.updateList);

    },
    destroyed() {

        this.$events.$off("form.update", this.updateList);

    },
    methods: {
        //Find current blockid
        findId (parent) {

            if (parent) {
                this.parent = parent.$parent?.$options?.propsData?.id ?? false;
            } else {
                this.parent = "";
            }

            if (typeof this.parent == "string") {
                this.updateList();
                return;
            }

            this.findId(parent.$parent);
        },
        updateList () {
            let $this = this
            this.$api.get("form/get-requests", {page: this.thisPage, form: ((this.parent) ? this.parent : "")})
            .then( (data) => {

                this.data = Object.keys(data).map(function (key) {

                    data[key].content = data[key].content.map((req) => {
                        req.attachment = 'attachment' in req ? JSON.parse(req.attachment) : false;
                        req.formdata = JSON.parse(req.formdata);
                        req.formfields = 'formfields' in req ? JSON.parse(req.formfields) : false;
                        req.status = $this.getStatus(req);
                        req.tooltip = $this.getTooltip(req);
                        let thisDate = $this.$library.dayjs(req.received, 'YYYY-MM-DD HH:mm:ss');
                        req.desc = thisDate.isValid() ? thisDate.format($this.dateformat) : "";
                        req.title = $this.getLabel(req);
                        return req;
                    });
                    return data[key]
                });
                
                this.loading = false;
            })
        },
        openMail (request) {
            this.current = request;
            this.$refs.dialog.open();

        },
        setRead (state, request = false) {

            if (!request) 
                request = this.current;

            this.$api.get("form/set-read", { form: request.parent, request: request.slug , state: state }).then((data) => {

                if (data){
                    this.$events.$emit("form.update");
                    this.$refs.dialog.close();
                }
                
            })
        },
        setAccordion (form, value) {
            this.$api.get("form/setAccodion", { form: (form), value: value }).then(() => {
                this.$events.$emit("form.update");
            })
        },
        deleteMail (request) {
            
            this.$api.get("form/delete-request", { form: request.parent, request: request.slug }).then(() => {
                this.$events.$emit("form.update");
            })
        },
        getLabel (req) {

            if(req.display)
                return req.display;

            if (!this.value) 
                return req.id;

            return this.$helper.string.template(this.value, req.formdata);
        },
        getStatus (req) {
            if (req.read)
                return "unlisted";

            if (req.error)
                return "draft";

            return "listed";

        },
        getTooltip (req) {

            if (req.error != "")
                return req.error;

            if (req.read != "")
                return this.$t("form.block.inbox.tooltip.read");

            return this.$t('form.block.inbox.tooltip.unread');

        },
    }
};
</script>

<style lang="scss">

    .k-field-type-page-dialog h2.k-headline {
        padding-bottom:15px;
    }

    .k-field-type-page-dialog-table {
        width:100%;
        padding: 15px;
        background: var(--color-gray-100);
        td, th {
            vertical-align: top;
            line-height: 1.25em;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        tr > td:first-child {
            padding-right: 1em;
        }
        
        .field_summary {
            display:none;
        }
        .k-field-type-page-change-display {
            padding-top: 3px;
        }
        .k-field-type-page-dialog-link {
            display: flex;
            font-size: 0.9em;
            line-height: 1.4em;
            span.k-icon {
                --size: 0.99em;
                margin-right: 6px;
            }
        }
    }

</style>