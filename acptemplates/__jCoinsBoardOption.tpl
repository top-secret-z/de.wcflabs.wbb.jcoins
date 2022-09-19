{if MODULE_JCOINS}
    <section class="section" id="jcoinsContainer">
        <h2 class="sectionTitle">{lang}wbb.acp.board.jcoins{/lang}</h2>

        <dl>
            <dd>
                <label><input type="checkbox" id="customJCoins" name="customJCoins" value="1"{if $customJCoins} checked="checked"{/if} /> {lang}wbb.acp.board.jcoins.custom{/lang}</label>
            </dd>
        </dl>

        <dl class="customJCoinsOption">
            <dt><label for="customJCoinsCreateThread">{lang}wbb.acp.board.jcoins.thread.customval{/lang}</label></dt>
            <dd>
                <input type="number" id="customJCoinsAmountCreateThread" name="customJCoinsAmountCreateThread" value="{$customJCoinsAmountCreateThread}" class="tiny" />
            </dd>
        </dl>

        <dl class="customJCoinsOption">
            <dt><label for="customJCoinsRetractableAmountCreateThread">{lang}wbb.acp.board.jcoins.thread.retractable.customval{/lang}</label></dt>
            <dd>
                <input type="number" id="customJCoinsRetractableAmountCreateThread" name="customJCoinsRetractableAmountCreateThread" value="{$customJCoinsRetractableAmountCreateThread}" class="tiny" />
            </dd>
        </dl>

        <dl class="customJCoinsOption">
            <dt><label for="customJCoinsCreatePost">{lang}wbb.acp.board.jcoins.post.customval{/lang}</label></dt>
            <dd>
                <input type="number" id="customJCoinsAmountCreatePost" name="customJCoinsAmountCreatePost" value="{$customJCoinsAmountCreatePost}" class="tiny" />
            </dd>
        </dl>

        <dl class="customJCoinsOption">
            <dt><label for="customJCoinsRetractableAmountCreatePost">{lang}wbb.acp.board.jcoins.post.retractable.customval{/lang}</label></dt>
            <dd>
                <input type="number" id="customJCoinsRetractableAmountCreatePost" name="customJCoinsRetractableAmountCreatePost" value="{$customJCoinsRetractableAmountCreatePost}" class="tiny" />
            </dd>
        </dl>

        {event name='jcoinsBoardField'}
    </section>

    <script data-relocate="true">
        $(function() {
            if({@$customJCoins}) {
                $('.customJCoinsOption').show();
            } else {
                $('.customJCoinsOption').hide();
            }

            $('#customJCoins').change(function() {
                if ($('#customJCoins:checked').val() == 1) {
                    $('.customJCoinsOption').show();
                } else {
                    $('.customJCoinsOption').hide();
                }
            });
        });
    </script>
{/if}
