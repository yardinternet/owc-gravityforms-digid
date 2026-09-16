<?php $tag = empty($vars['link']) ? 'div' : 'a'; ?>
<?php if (! empty($vars['error'])) : ?>
    <div class="owc-gf-digid-alert">{{ error }}</div>
<?php endif; ?>

<<?php echo $tag; ?><?php if ('a' === $tag) : ?> href="{{ link }}"<?php endif; ?> class="digid-btn">
    <img class="digid-btn__img" src="{{ logo }}" alt="DigiD logo">
    <div class="digid-btn__text">
        <div class="digid-btn__title">{{ title }}</div>
        <?php if (! empty($vars['subtitle'])) : ?>
        <div class="digid-btn__subtitle">{{ subtitle }}</div>
        <?php endif; ?>
    </div>
</<?php echo $tag; ?>>
