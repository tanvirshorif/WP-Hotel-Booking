<?php
/**
 * Admin View: Addition packages setting page.
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

hb_admin_view( 'extra/item' );
hb_admin_view( 'extra/new' );
?>

<div id="wphb-admin-extra-panel"></div>

<script type="text/x-template" id="tmpl-admin-extra-panel">
    <div>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e( 'Addition Packages', 'wp-hotel-booking' ); ?></h1>
            <a class="page-title-action" @click="addExtra"><?php echo __( 'Add New', 'wp-hotel-booking' ); ?></a>
        </div>
        <div id="wphb-admin-extra-panel">
            <div class="extra-items-heading">
                <div class="name">
                    <h4><?php _e( 'Name', 'wp-hotel-booking' ); ?></h4>
                </div>
                <div class="desc">
                    <h4><?php _e( 'Description', 'wp-hotel-booking' ); ?></h4>
                </div>
                <div class="price">
                    <h4><?php _e( 'Price', 'wp-hotel-booking' ); ?></h4>
                </div>
                <div class="type">
                    <h4><?php _e( 'Price Type', 'wp-hotel-booking' ); ?></h4>
                </div>
                <div class="actions">
                    <h4><?php _e( 'Actions', 'wp-hotel-booking' ); ?></h4>
                </div>
            </div>

            <template v-for="(item, index) in extra">
                <wphb-admin-extra-item :extra="item" :index="index" :types="types"
                                       @updateExtra="updateExtra" @deleteExtra="deleteExtra"></wphb-admin-extra-item>
            </template>
            <wphb-admin-extra-new v-if="add || !extra.length" :types="types" @newExtra="newExtra"
                                  @deleteNew="deleteNew"></wphb-admin-extra-new>

            <button class="button update-items button-primary"
                    @click="updateItems"><?php echo __( 'Update', 'wp-hotel-booking' ); ?></button>
        </div>
    </div>
</script>

<script type="text/javascript">
    (function (Vue, $store) {

        Vue.component('wphb-extra-panel', {
            template: '#tmpl-admin-extra-panel',
            data: function () {
                return {
                    add: false
                }
            },
            computed: {
                addable: function () {
                    return $store.getters['addable'];
                },
                extra: function () {
                    return $store.getters['extra'];
                },
                types: function () {
                    return $store.getters['types'];
                }
            },
            methods: {
                addExtra: function () {
                    if (this.addable) {
                        this.add = true;
                    }
                },
                updateExtra: function (extra) {
                    $store.dispatch('updateExtra', extra);
                },
                newExtra: function (extra) {
                    $store.dispatch('newExtra', extra);
                    this.add = false;
                },
                deleteExtra: function (payload) {
                    $store.dispatch('deleteExtra', payload);
                },
                deleteNew: function () {
                    this.add = false;
                },
                updateItems: function () {
                    $store.dispatch('updateListExtra', this.extra);
                }
            }
        })

    })(Vue, WPHB_Extra_Store);
</script>
