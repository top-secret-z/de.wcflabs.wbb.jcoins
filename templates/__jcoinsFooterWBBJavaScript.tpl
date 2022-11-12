{*
 * This file is located in the footer, because it must be the last JavaScript which is executed ;
 * because we unbind some buttons
 *}
{if $templateName == 'thread' && $hasEnougthJCoins|isset && !$hasEnougthJCoins}
    <script data-relocate="true">
        require(['Language'], function(Language) {
            Language.addObject({
                'wcf.jcoins.amount.posting.tooLow': '{lang}wcf.jcoins.amount.posting.tooLow{/lang}'
            });

            elBySel('#messageQuickReply').innerHTML = '<p class="info">'+ Language.get('wcf.jcoins.amount.posting.tooLow') +'</p>'
        });
    </script>
{/if}
