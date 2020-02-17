import Autocomplete from './autocomplete';

export default (() => {
    const searchIpaInput = document.querySelector('input#public_administration_name[data-search="searchIpa"]');
    const ipaCodeInput = document.getElementById('ipa_code');
    const urlInput = document.getElementById('url');
    const rtdNameInput = document.getElementById('rtd_name');
    const rtdMailInput = document.getElementById('rtd_mail');
    const urlInputLabel = document.querySelector('label[for="url"]');
    const rtdNameInputLabel = document.querySelector('label[for="rtd_name"]');
    const rtdMailInputLabel = document.querySelector('label[for="rtd_mail"]');
    const rtdMailPresentMessage = document.getElementById('rtd_mail_present');
    const rtdMailMissingMessage = document.getElementById('rtd_mail_missing');
    const primaryWebsiteMissingMessage = document.getElementById('primary_website_missing');

    const handleSelectedIpa = selectedResult => {
        ipaCodeInput.value = selectedResult.ipa_code;
        urlInput.value = selectedResult.site;
        rtdNameInput.value = selectedResult.rtd_name || '';
        rtdMailInput.value = selectedResult.rtd_mail || '';
        urlInputLabel.classList.add('active');
        rtdNameInputLabel.classList.add('active');
        rtdMailInputLabel.classList.add('active');
        toggleMissingPrimaryWebsiteMessage() && toggleRtdMessage();
    };

    const toggleMissingPrimaryWebsiteMessage = () => {
        ipaCodeInput.value && (urlInput.value || primaryWebsiteMissingMessage.classList.remove('d-none'));

        return !!urlInput.value;
    }

    const toggleRtdMessage = () => {
        ipaCodeInput.value && (rtdMailInput.value && rtdMailPresentMessage.classList.remove('d-none'));
        ipaCodeInput.value && (rtdMailInput.value || rtdMailMissingMessage.classList.remove('d-none'));
    }

    const onIpaSearch = () => {
        ipaCodeInput.value = '';
        urlInput.value = '';
        rtdNameInput.value = '';
        rtdMailInput.value = '';
        urlInput.classList.remove('is-invalid', 'is-valid');
        rtdNameInput.classList.remove('is-invalid', 'is-valid');
        rtdMailInput.classList.remove('is-invalid', 'is-valid');
        urlInputLabel.classList.remove('active');
        rtdNameInputLabel.classList.remove('active');
        rtdMailInputLabel.classList.remove('active');
        primaryWebsiteMissingMessage.classList.add('d-none')
        rtdMailMissingMessage.classList.add('d-none');
        rtdMailPresentMessage.classList.add('d-none');
    };

    const ipaSchema = {
        title: 'name',
        subTitle: 'city',
    }

    const init = () => {
        searchIpaInput && Autocomplete.init(searchIpaInput, ipaSchema, {
            handleSelectedResult: handleSelectedIpa,
            onSearch: onIpaSearch,
        });
        toggleMissingPrimaryWebsiteMessage() && toggleRtdMessage();
    };

    return { init };
})();
