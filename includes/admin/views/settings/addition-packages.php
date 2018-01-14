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

hb_admin_view( 'settings/extra/item' );
hb_admin_view( 'settings/extra/new' );
?>

<div id="wphb-admin-extra-panel"></div>

<script type="text/x-template" id="tmpl-admin-extra-panel">
    <div>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e( 'Addition Packages', 'wp-hotel-booking' ); ?></h1>
            <a href="#" class="page-title-action"
               @click="addExtra"><?php echo __( 'Add New', 'wp-hotel-booking' ); ?></a>
        </div>
        <div id="wphb-admin-extra-panel">
            <template v-for="(item, index) in extra">
                <wphb-admin-extra-item :extra="item" :index="index" :types="types"
                                       @updateExtra="updateExtra" @deleteExtra="deleteExtra"></wphb-admin-extra-item>
            </template>
            <wphb-admin-extra-new v-if="add" :types="types" @newExtra="newExtra"
                                  @deleteNew="deleteNew"></wphb-admin-extra-new>
        </div>

        <button class="button update-items button-primary"
                @click="updateItems"><?php echo __( 'Update', 'wp-hotel-booking' ); ?></button>
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
                    // code
                }
            }
        })

    })(Vue, WPHB_Extra_Store);
</script>
