<?php

namespace LiveChat\LiveChat\Block\System\Config\Fieldset;

class AccountSettintsGroup extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Helper\Js $jsHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * Return header comment part of html for fieldset
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        $licenseId = $this->getFieldValue($element, 'lc_block_config_account_license_id');
        $licenseEmail = $this->getFieldValue($element, 'lc_block_config_account_license_email');

        if (null !== $licenseEmail && null !== $licenseId) {
            $content =
'<a target="_blank" href="https://www.livechatinc.com/">Help</a> |
<a target="_blank" href="https://www.livechatinc.com/product/">Download LiveChat desktop APP</a> |
<a target="_blank" href="https://my.livechatinc.com/">Launch LiveChat web APP</a><br /><br />
Your LiveChat account is connected to your Magento.<br />
<br />Email: ' . $licenseEmail . '<br />License id: ' . $licenseId . '<br/>
Status: <span id="livechat-account-status" data-license-id="' . $licenseId. '">connecting...</span><br /><br />
<a href="#" id="lc_change_account" onclick="javascript:LiveChatForm(); return false;">Connect a different account</a>';
            $content .= $this->getLiveChatForm(true, $licenseEmail);
        } else {
            $content = '<span class="lc_use_existing_account">'
                    . 'LiveChat account is not yet connected to your Magento. Connect your account below.'
                    . '</span>'
                    . '<span class="lc_new_account">'
                    . 'To create a LiveChat account, enter your email address and choose a password below, then click on \'Create account and connect\'.'
                    . '</span><br /><br />'
                    . 'See <a target="_blank" href="https://www.livechatinc.com/">the tutorial</a> if you need help.';
            $content .= $this->getLiveChatForm(false);
        }

        $html = '<div class="comment" id="livechat-settings-comment">' . $content . '</div>';

        return $html;
    }

    /**
     * Returns livechat form
     *
     * @param boolean $hidden
     * @param string $licenseEmail
     * @return string
     */
    private function getLiveChatForm($hidden = false, $licenseEmail = null)
    {
return '<table cellspacing="0" class="form-list ' . ($hidden? 'hidden' : '') . '" id="lc_form">
    <tbody>
        <tr class="lc_use_existing_account">
            <td class="label">
                <label for="lc_existing_account">I have an existing LiveChat account</label>
            </td>
            <td class="value">
                <input id="lc_existing_account" name="lc_existing_account" value="' . ($licenseEmail ? $licenseEmail : '') . '" class="validate-email input-text admin__control-text" type="text">
                <label for="lc_existing_account" class="mage-error lc-hidden" id="lc_existing_account-error"></label>
                <p class="note">
                    <span>
                        Enter your existing LiveChat email.
                    </span>
                </p>
            </td>
            <td class="scope-label"></td>
            <td class=""></td>
        </tr>
        <tr class="lc_use_existing_account">
            <td class="label">
                <label for="lc_use_existing_account_button"></label>
            </td>
            <td class="value">
                <div class="actions">
                    <button onclick="javascript:LiveChatConnect(); return false;" type="button" id="lc_use_existing_account_button">
                        <span>Connect</span>
                    </button>
                    &nbsp;&nbsp;or&nbsp;
                    <a href="#" id="create-new-account" onclick="javascript:LiveChatNewAccountForm(); return false;">
                        Create LiveChat account for free
                    </a>
                </div>
            </td>
            <td class="scope-label"></td>
            <td class=""></td>
        </tr>
        <tr class="lc_new_account">
            <td class="label">
                <label for="lc_new_account_email">Enter email address</label>
            </td>
            <td class="value">
                <input id="lc_new_account_email" name="lc_new_account_email" class="validate-email input-text admin__control-text" type="text">
                <label for="lc_new_account_email" class="mage-error lc-hidden" id="lc_new_account_email-error"></label>
                <p class="note"><span>You\'ll use it to log in to LiveChat.</span></p>
            </td>
            <td class="scope-label"></td>
            <td class=""></td>
        </tr>
        <tr class="lc_new_account">
            <td class="label">
                <label for="lc_new_account_password">Create password</label>
            </td>
            <td class="value">
                <input id="lc_new_account_password" name="lc_new_account_password" class="input-text admin__control-text" type="password">
                <label for="lc_new_account_password" class="mage-error lc-hidden" id="lc_new_account_password-error"></label>
                <p class="note"><span>Must be at least 6 characters.</span></p>
            </td>
            <td class="scope-label"></td>
            <td class=""></td>
        </tr>
        <tr class="lc_new_account">
            <td class="label">
                <label for="lc_create_new_account_button"></label>
            </td>
            <td class="value">
                <div class="actions">
                    <button onclick="javascript:LiveChatCreateNewAccount(); return false;" type="button" id="lc_create_new_account_button">
                        <span>Create account</span>
                    </button>
                    &nbsp;&nbsp;or&nbsp;
                    <a href="#" id="use-existing-account" onclick="javascript:LiveChatExistingAccountForm(); return false;">Use an existing one</a>
                </div>
            </td>
            <td class="scope-label"></td>
            <td class=""></td>
        </tr>
        ' . ($licenseEmail ? '<tr class="lc_use_existing_account">
            <td class="label">
                <a href="#" class="lc-disconnect" onclick="javascript:LiveChatDisconnect(); return false;">
                    Disconnect
                </a>
            </td>
        </tr>' : '') . '
    </tbody>
</table>';
    }

    /**
     * Return value for field
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    private function getFieldValue($element, $fieldName)
    {
        $fieldElement= $element->getElements()->searchById($fieldName);
        if (!is_object($fieldElement) || !method_exists($fieldElement, 'getData')) {
            return null;
        }

        $fieldElementData = $fieldElement->getData();

        if (array_key_exists('value', $fieldElementData)) {
            return $fieldElementData['value'];
        }

        return null;
    }
}
