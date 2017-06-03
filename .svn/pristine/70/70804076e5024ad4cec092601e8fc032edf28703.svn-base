 /**
 * $Id: cite.js 318 2015-04-27 00:21:26Z xiao5 $
 *
 * @author Moxiecode - based on work by Andrew Tetlaw
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

function init() {
    SXE.initElementDialog('cite');
    if (SXE.currentAction == "update") {
        SXE.showRemoveButton();
    }
}

function insertCite() {
    SXE.insertElement('cite');
    tinyMCEPopup.close();
}

function removeCite() {
    SXE.removeElement('cite');
    tinyMCEPopup.close();
}

tinyMCEPopup.onInit.add(init);
