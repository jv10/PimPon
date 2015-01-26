pimcore.registerNS("pimcore.plugin.pimpon.handler.x");
// OBJECT TREE HANDLER
pimcore.plugin.pimpon.handler.exportobject = function () {
    var url = '/plugin/PimPon/object/export?objectId=' + this.id;
    Ext.MessageBox.confirm('Confirm', '¿Quieres exportar el objeto con ID: ' + this.id + ' junto con todos sus hijos?', function (btn) {
        if (btn === 'yes') {
            location.href = url;
        }
    });
};

pimcore.plugin.pimpon.handler.importobject = function () {
    var url = '/plugin/PimPon/object/import?objectId=' + this.id;
    pimcore.helpers.uploadDialog(url, "Filedata", function (response) {

        response = response.response;
        pimcore.helpers.showNotification("PimPon Plugin", "Objetos importados con éxito", "success");

    }.bind(this), function () {
        Ext.MessageBox.alert(t("error"), "Ha ocurrido un error en la importacion de objetos a Pimcore.");
    });
};

// DOCUMENT TREE HANDLER
pimcore.plugin.pimpon.handler.exportdocument = function () {
    var url = '/plugin/PimPon/document/export?documentId=' + this.id;
    Ext.MessageBox.confirm('Confirm', '¿Quieres exportar el documento con ID: ' + this.id + ' junto con todos sus hijos?', function (btn) {
        if (btn === 'yes') {
            location.href = url;
        }
    });
};

pimcore.plugin.pimpon.handler.importdocument = function () {
    var url = '/plugin/PimPon/document/import?documentId=' + this.id;
    pimcore.helpers.uploadDialog(url, "Filedata", function (response) {
        response = response.response;
        pimcore.helpers.showNotification("PimPon Plugin", "Documentos importados con éxito", "success");
    }.bind(this), function () {
        Ext.MessageBox.alert(t("error"), "Ha ocurrido un error en la importacion de documentos a Pimcore.");
    });
};

// ROUTES TOOLBAR HANDLER
pimcore.plugin.pimpon.handler.exportroutes = function () {
    var url = '/plugin/PimPon/route/export';
    Ext.MessageBox.confirm('Confirm', '¿Quieres exportar todas las rutas estaticas?', function (btn) {
        if (btn === 'yes') {
            location.href = url;
        }
    });
};

pimcore.plugin.pimpon.handler.importroutes = function () {
    var url = '/plugin/PimPon/route/import';
    pimcore.helpers.uploadDialog(url, "Filedata", function (response) {
        response = response.response;
        pimcore.helpers.showNotification("PimPon Plugin", "Rutas estaticas importadas con éxito", "success");
    }.bind(this), function () {
        Ext.MessageBox.alert(t("error"), "Ha ocurrido un error en la importacion de rutas a Pimcore.");
    });
};


// USERS TREE HANDLER
pimcore.plugin.pimpon.handler.exportusers = function () {
    var url = '/plugin/PimPon/user/export?userId=' + this.id;
    Ext.MessageBox.confirm('Confirm', '¿Quieres exportar todos los usuario de Pimcore?', function (btn) {
        if (btn === 'yes') {
            location.href = url;
        }
    });
};

pimcore.plugin.pimpon.handler.importusers = function () {
    var url = '/plugin/PimPon/user/import?userId=' + this.id;
    pimcore.helpers.uploadDialog(url, "Filedata", function (response) {
        response = response.response;
        pimcore.helpers.showNotification("PimPon Plugin", "Usuarios importados con éxito", "success");
    }.bind(this), function () {
        Ext.MessageBox.alert(t("error"), "Ha ocurrido un error en la importacion de usuarios a Pimcore.");
    });
};

// ROLES TREE HANDLER
pimcore.plugin.pimpon.handler.exportroles = function () {
    var url = '/plugin/PimPon/role/export?roleId=' + this.id;
    console.log(this);
    Ext.MessageBox.confirm('Confirm', '¿Quieres exportar el rol <b>'+this.text+'</b> y todos sus descendientes?', function (btn) {
        if (btn === 'yes') {
            location.href = url;
        }
    });
};

pimcore.plugin.pimpon.handler.importroles = function () {
    var url = '/plugin/PimPon/role/import?roleId=' + this.id;
    pimcore.helpers.uploadDialog(url, "Filedata", function (response) {
        response = response.response;
        pimcore.helpers.showNotification("PimPon Plugin", "Roles importadas con éxito", "success");
    }.bind(this), function () {
        Ext.MessageBox.alert(t("error"), "Ha ocurrido un error en la importacion de roles a Pimcore.");
    });
};