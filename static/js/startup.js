pimcore.registerNS("pimcore.plugin.pimpon");

pimcore.plugin.pimpon = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.pimpon";
    },
    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },
    pimcoreReady: function (params, broker) {

        Ext.override(pimcore.settings.user.panel, {
            onTreeNodeContextmenu: function () {
                console.log('contexmenu user');
                var user = pimcore.globalmanager.get("user");
                if (user.admin == false) {
                    return;
                }

                this.select();
                var menu = new Ext.menu.Menu();

                if (this.allowChildren) {
                    menu.add(new Ext.menu.Item({
                        text: t('add_folder'),
                        iconCls: "pimcore_icon_folder_add",
                        listeners: {
                            "click": this.attributes.reference.add.bind(this, "userfolder", 0)
                        }
                    }));
                    menu.add(new Ext.menu.Item({
                        text: t('add_user'),
                        iconCls: "pimcore_icon_user_add",
                        listeners: {
                            "click": this.attributes.reference.add.bind(this, "user", 0)
                        }
                    }));
                } else if (this.attributes.elementType == "user") {
                    menu.add(new Ext.menu.Item({
                        text: t('clone_user'),
                        iconCls: "pimcore_icon_user_add",
                        listeners: {
                            "click": this.attributes.reference.add.bind(this, "user", this.attributes.id)
                        }
                    }));
                }

                ///////////////////////////////////////////////////////////
                // PIMPON PLUGIN
                var user = pimcore.globalmanager.get("user");
                if (user.admin) { // only admins are allowed to change locks in frontend
                    var pimponMenu = [];
                    if (this.allowChildren) {
                        pimponMenu.push({
                            text: 'Importar Usuarios',
                            iconCls: "pimpon_icon_importusers",
                            handler: pimcore.plugin.pimpon.handler.importusers.bind(this)
                        });
                    }
                    pimponMenu.push({
                        text: 'Exportar Usuarios',
                        iconCls: "pimpon_icon_exportusers",
                        handler: pimcore.plugin.pimpon.handler.exportusers.bind(this)
                    });
                    menu.add(new Ext.menu.Item({
                        text: 'PimPon Plugin',
                        iconCls: "pimpon_icon_plugin",
                        hideOnClick: false,
                        menu: pimponMenu
                    }));
                }
                ///////////////////////////////////////////////////////////    

                if (this.id != user.id) {
                    menu.add(new Ext.menu.Item({
                        text: t('delete'),
                        iconCls: "pimcore_icon_delete",
                        listeners: {
                            "click": this.attributes.reference.remove.bind(this)
                        }
                    }));
                }

                if (typeof menu.items != "undefined" && typeof menu.items.items != "undefined"
                        && menu.items.items.length > 0) {
                    menu.show(this.ui.getAnchor());
                }
            }

        });


        Ext.override(pimcore.settings.user.role.panel, {
            onTreeNodeContextmenu: function () {
                console.log('contextmenu role');
                var user = pimcore.globalmanager.get("user");
                if (user.admin == false) {
                    return;
                }

                this.select();
                var menu = new Ext.menu.Menu();

                if (this.allowChildren) {
                    menu.add(new Ext.menu.Item({
                        text: t('add_folder'),
                        iconCls: "pimcore_icon_folder_add",
                        listeners: {
                            "click": this.attributes.reference.add.bind(this, "rolefolder", 0)
                        }
                    }));
                    menu.add(new Ext.menu.Item({
                        text: t('add_role'),
                        iconCls: "pimcore_icon_role_add",
                        listeners: {
                            "click": this.attributes.reference.add.bind(this, "role", 0)
                        }
                    }));
                } else if (this.attributes.elementType == "role") {
                    menu.add(new Ext.menu.Item({
                        text: t('clone_role'),
                        iconCls: "pimcore_icon_role_add",
                        listeners: {
                            "click": this.attributes.reference.add.bind(this, "role", this.attributes.id)
                        }
                    }));
                }

                ///////////////////////////////////////////////////////////
                // PIMPON PLUGIN
                var user = pimcore.globalmanager.get("user");
                if (user.admin) { // only admins are allowed to change locks in frontend
                    var pimponMenu = [];
                    if (this.allowChildren) {
                        pimponMenu.push({
                            text: 'Importar Roles',
                            iconCls: "pimpon_icon_importroles",
                            handler: pimcore.plugin.pimpon.handler.importroles.bind(this)
                        });
                    }
                    pimponMenu.push({
                        text: 'Exportar Roles',
                        iconCls: "pimpon_icon_exportroles",
                        handler: pimcore.plugin.pimpon.handler.exportroles.bind(this)
                    });
                    menu.add(new Ext.menu.Item({
                        text: 'PimPon Plugin',
                        iconCls: "pimpon_icon_plugin",
                        hideOnClick: false,
                        menu: pimponMenu
                    }));
                }
                ///////////////////////////////////////////////////////////    

                menu.add(new Ext.menu.Item({
                    text: t('delete'),
                    iconCls: "pimcore_icon_delete",
                    listeners: {
                        "click": this.attributes.reference.remove.bind(this)
                    }
                }));

                if (typeof menu.items != "undefined" && typeof menu.items.items != "undefined"
                        && menu.items.items.length > 0) {
                    menu.show(this.ui.getAnchor());
                }
            }
        });

    }
});

var pimponPlugin = new pimcore.plugin.pimpon();

