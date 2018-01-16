<?php
/**
 * Admin View: Addition packages new item Vue component.
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

<script type="text/x-template" id="tmpl-admin-extra-new">
    <div class="extra-item new-item">
        <div class="name">
            <h4><?php _e( 'Name', 'wp-hotel-booking' ); ?></h4>
            <input type="text" v-model="extra.title"/>
        </div>
        <div class="desc">
            <h4><?php _e( 'Description', 'wp-hotel-booking' ); ?></h4>
            <textarea v-model="extra.description"></textarea>
        </div>
        <div class="price">
            <h4><?php _e( 'Price', 'wp-hotel-booking' ); ?></h4>
            <input type="number" min=0 v-model="extra.price"/>
            <span>/</span>
            <input type="text" v-model="extra.unit"
                   placeholder="<?php esc_attr_e( 'Package', 'wp-hotel-booking' ); ?>"/>
        </div>
        <div class="type">
            <h4><?php _e( 'Price Type', 'wp-hotel-booking' ); ?></h4>
            <select v-model="extra.type">
                <option v-for="(name, key) in types" v-bind:value="key">{{name}}</option>
            </select>
        </div>
        <div class="save"><a class="button dashicons dashicons-welcome-add-page" @click="newExtra"></a></div>
        <div class="remove"><a class="button dashicons dashicons-trash" @click="deleteNew"></a></div>
    </div>
</script>

<script type="text/javascript">
    (function (Vue, $store) {

        Vue.component('wphb-admin-extra-new', {
            template: '#tmpl-admin-extra-new',
            data: function () {
                return {
                    extra: {
                        id: '',
                        title: '',
                        description: '',
                        price: '',
                        unit: '',
                        type: Object.keys(this.types)[0]
                    }
                }
            },
            props: ['types'],
            methods: {
                newExtra: function () {
                    this.$emit('newExtra', this.extra);
                },
                deleteNew: function () {
                    this.$emit('deleteNew');
                }
            }
        })

    })(Vue, WPHB_Extra_Store);
</script>
