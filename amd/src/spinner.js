define([], function() {
    return {
        init: function() {
            var iframeElem = document.getElementById('block_onlinesurveyqa_contentframe');
            var contentElem = document.getElementById('block_onlinesurveyqa_surveys_content');

            iframeElem.addEventListener('load', function() {
                contentElem.className = contentElem.className.replace(/block_onlinesurveyqa_is-loading/, '');
            }, true);
        }
    };
});
