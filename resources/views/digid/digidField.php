<?php if (isset($vars['error'])) : ?>
    <div class="alert" style="border: 1px solid red; font-size: 0.9rem;">{{ error }}</div>
<?php endif; ?>

<a href="{{ link }}" class="digid-btn" style="display: flex; align-items: center;">
    <img class="digid-btn__img" src="{{ logo }}" alt="DigiD logo">
    <div class="digid-btn__text" style="padding-left: 0.5rem">
        <div class="digid-btn__title">{{ title }}</div>
        <div class="digid-btn__subtitle">{{ subtitle }}</div>
    </div>
</a>