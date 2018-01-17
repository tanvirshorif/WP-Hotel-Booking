<?php
/**
 * Admin View: Addition packages item Vue component.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<script type="text/x-template" id="tmpl-admin-extra-item">
    <div :class="['extra-item', 'item-id-' + extra.id]">
        <div class="name">
            <input type="text" v-model="extra.title"/>
        </div>
        <div class="desc">
            <textarea rows="2" v-model="extra.description"></textarea>
        </div>
        <div class="price">
            <input type="number" min=0 v-model="extra.price" class="price"/>
            <span>/</span>
            <input type="text" v-model="extra.unit" class="unit"
                   placeholder="<?php esc_attr_e( 'Package', 'wp-hotel-booking' ); ?>"/>
        </div>
        <div class="type">
            <select v-model="extra.type">
                <option v-for="(name, key) in types" v-bind:value="key">{{name}}</option>
            </select>
        </div>
        <div class="actions">
            <a class="dashicons dashicons-welcome-write-blog update" @click="updateExtra"
               title="<?php esc_attr_e( 'Save', 'wp-hotel-booking' ); ?>"></a>
            <a class="dashicons dashicons-trash delete" @click="deleteExtra"
               title="<?php esc_attr_e( 'Delete', 'wp-hotel-booking' ); ?>"></a>
        </div>

    </div>
</script>

<script type="text/javascript">
    (function (Vue, $store) {

        Vue.component('wphb-admin-extra-item', {
            template: '#tmpl-admin-extra-item',
            props: ['extra', 'index', 'types'],
            methods: {
                updateExtra: function () {
                    this.$emit('updateExtra', this.extra);
                },
                deleteExtra: function () {
                    this.$emit('deleteExtra', [{extra_id: this.extra.id, index: this.index}]);
                }
            }
        })

    })(Vue, WPHB_Extra_Store);
</script>
