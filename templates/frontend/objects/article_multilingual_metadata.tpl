{*
 * Show article keywords and abstract in ui, or submission, language by default.
 * Show optional multilingual metadata: titles, keywords, abstracts.
 *}
{foreach from=$pubLocaleData.locales item=locale}
    {* Only show if some of these *}
    {if    !isset($pubLocaleData.title.text[$locale])
        && !isset($pubLocaleData.keywords.text[$locale])
        && !isset($pubLocaleData.abstract.text[$locale])
    }
        {continue}
    {/if}

    {* Show in current ui locale by default or else in another possible supported ui locale *}
    {assign var="supportedLocale" value=$currentLocale}
    {if isset($pubLocaleData.localeNames[$locale])}
        {assign var="supportedLocale" value=$locale}
    {/if}

    {assign "hLvl" "2"}
    <section class="metadata multilingual-{$locale}">
    {* Multilingual metadata title *}
    {if $locale !== $pubLocaleData.titleLocale}
        {assign "hLvl" "3"}
        <h2 class="item label page_metadata_title" lang={$supportedLocale|replace:"_":"-"}>
            {translate key="plugins.themes.default.submissionMetadataInLanguage" locale=$supportedLocale inlang=$pubLocaleData.localeNames[$supportedLocale][$locale]}
        </h2>
        {* Title in other language *}
        {if isset($pubLocaleData.title.text[$locale])}
            <section class="item page_locale_title">
                <h{$hLvl} class="label" lang="{$pubLocaleData.title.hLang[$locale]|replace:"_":"-"}">
                    {translate key="submission.title" locale=$pubLocaleData.title.hLang[$locale]}
                </h{$hLvl}>
                <p lang="{$locale|replace:"_":"-"}">
                    {$pubLocaleData.title.text[$locale]|strip_tags}
                    {if isset($pubLocaleData.subtitle.text[$locale])}
                        {translate key="plugins.themes.default.titleSubtitleSeparator" locale=$pubLocaleData.title.hLang[$locale]}{$pubLocaleData.subtitle.text[$locale]|strip_tags}
                    {/if}
                </p>
            </section>
        {/if}
    {/if}

    {* Keywords *}
    {if isset($pubLocaleData.keywords.text[$locale])}
        <section class="item keywords">
            <h{$hLvl} class="label" lang="{$pubLocaleData.keywords.hLang[$locale]|replace:"_":"-"}">
                {translate key="common.keywords" locale=$pubLocaleData.keywords.hLang[$locale]}
            </h{$hLvl}>
            <p class="value" lang="{$locale|replace:"_":"-"}">
            {foreach from=$pubLocaleData.keywords.text[$locale] item="keyword"}
                {$keyword|escape}{if !$keyword@last}{translate key="common.commaListSeparator" locale=$pubLocaleData.keywords.hLang[$locale]}{/if}
            {/foreach}
            </p>
        </section>
    {/if}

    {* Abstract *}
    {if isset($pubLocaleData.abstract.text[$locale])}
        <section class="item abstract">
            <h{$hLvl} class="label" lang="{$pubLocaleData.abstract.hLang[$locale]|replace:"_":"-"}">
                {translate key="common.abstract" locale=$pubLocaleData.abstract.hLang[$locale]}
            </h{$hLvl}>
            <p lang="{$locale|replace:"_":"-"}">{$pubLocaleData.abstract.text[$locale]|strip_tags}</p>
        </section>
    {/if}

    {call_hook name="Templates::Article::Main"}
    </section>
{/foreach}