/**
 * Created by Nef1k on 18.09.2016.
 */

function CertificateList(){
    this.__certificate_list = [];

    this.add = function(id) {
        var indexToAdd = this.__certificate_list.indexOf(id);
        if (indexToAdd < 0) {
            this.__certificate_list.push(id);
        }
    };

    this.remove = function(id) {
        var indexToDelete = this.__certificate_list.indexOf(id);
        if (indexToDelete >= 0) {
            this.__certificate_list = this.__certificate_list.splice(indexToDelete, 1);
        }
    };
}