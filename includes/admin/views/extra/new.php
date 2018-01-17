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
            <input type="text" v-model="extra.title"
                   placeholder="<?php echo __( 'Package Name', 'wp-hotel-booking' ); ?>"/>
        </div>
        <div class="desc">
            <textarea v-model="extra.description"
                      placeholder="<?php echo __( 'Description', 'wp-hotel-booking' ); ?>"></textarea>
        </div>
        <div class="price">
            <input type="number" min="0" placeholder="10" v-model="extra.price" class="price"/>
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
            <a class="dashicons dashicons-welcome-add-page add" @click="newExtra"
               title="<?php esc_attr_e( 'Add new', 'wp-hotel-booking' ); ?>"></a>
            <a class="dashicons dashicons-trash delete" @click="deleteNew"
               title="<?php esc_attr_e( 'Delete', 'wp-hotel-booking' ); ?>"></a>
        </div>
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
                        price: 10,
                        unit: $store.getters['unit'],
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
