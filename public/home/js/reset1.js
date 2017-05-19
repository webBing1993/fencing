/**
 * Created by Administrator on 2017/5/19.
 */
//修改alert样式
var wAlert = window.alert;
window.alert = function (message) {
    try {
        var iframe = document.createElement("IFRAME");
        iframe.style.display = "none";
        iframe.setAttribute("src", 'data:text/plain,');
        document.documentElement.appendChild(iframe);
        var alertFrame = window.frames[0];
        var iwindow = alertFrame.window;
        if (iwindow == undefined) {
            iwindow = alertFrame.contentWindow;
        }
        var  a= iwindow.alert(message);
        iframe.parentNode.removeChild(iframe);
    }
    catch (exc) {
        return wAlert(message);
    }
};
var wConfirm = window.confirm;
window.confirm = function (message) {
    try {
        var iframe = document.createElement("IFRAME");
        iframe.style.display = "none";
        iframe.setAttribute("src", 'data:text/plain,');
        document.documentElement.appendChild(iframe);
        var alertFrame = window.frames[0];
        var iwindow = alertFrame.window;
        if (iwindow == undefined) {
            iwindow = alertFrame.contentWindow;
        }
        iwindow.confirm(message);
        var  a=iframe.parentNode.removeChild(iframe);
        return a;
    }
    catch (exc) {
        return wConfirm(message);
    }
}