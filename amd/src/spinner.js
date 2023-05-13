define([], function() {
    return {
        init: function(src) {
            var iframeElem = document.getElementById('block_onlinesurveyqa_contentframe');
            var contentElem = document.getElementById('block_onlinesurveyqa_surveys_content');

            iframeElem.src = src;
            iframeElem.addEventListener('load', function() {
                contentElem.className = contentElem.className.replace(/block_onlinesurveyqa_is-loading/, '');
            }, true);
        }
    };
});
