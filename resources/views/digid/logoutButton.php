<?php $tag = empty($vars['link']) ? 'div' : 'a'; ?>
<<?php echo $tag; ?><?php if ('a' === $tag) : ?> href="{{ link }}"<?php endif; ?> class="digid-btn">
    <img class="digid-btn__img" src="{{ logo }}" alt="DigiD logo">
    <div class="digid-btn__text">
        <div class="digid-btn__title">{{ title }}</div>
    </div>
</<?php echo $tag; ?>>
