Preview Mode
============

Preview Mode is an optional view of an object before it is persisted or updated.

The preview step can be enabled for an admin entity by overriding the public property
``$supportsPreviewMode`` and setting it to true::

    // src/AdminPostAdmin.php

    final class PostAdmin extends AbstractAdmin
    {
        public $supportsPreviewMode = true;
    }

This will show a new button during create/edit mode named preview.

.. figure:: ../images/preview_mode_button.png
   :align: center
   :alt: Preview Button

While in preview mode two buttons will be shown to approve or decline persistence of the
entity. Decline will send you back to the edit mode with all your changes unpersisted but
still in the form so no data is lost and the entity can be further adjusted.
Accepting the preview will store the entity as if the preview step was never there.

.. figure:: ../images/preview_show.png
   :align: center
   :alt: Preview Button

Simulating front-end rendering
------------------------------

Preview can be used to render how the object would look like in your front-end environment.

However by default it uses a template similar to the one of the show action and works with
the fields configured to be shown in the show view.

Overriding the preview template ``@SonataAdmin/CRUD/preview.html.twig`` can be done either
globally through the template configuration for the key 'preview':

.. configuration-block::

    .. code-block:: yaml

        # config/packages/sonata_admin.yaml

        sonata_admin:
            templates:
                preview: '@App/CRUD/preview.html.twig'

Or per admin entity by calling the ``setTemplate()`` method::

    // src/Admin/PostAdmin.php

    protected function configure()
    {
        $this->setTemplate('preview', '@App/CRUD/preview.html.twig');
    }

In either way the template should be extending your own layout, injecting the form in it
and finally overriding the action buttons to show the approve/decline buttons like the
default preview.html.twig.

The entity is passed to the view in a variable called **object**. If your original view
expects a different object you can set your own variables prior to calling ``parent()``.

.. code-block:: jinja

    {# '@App/CRUD/preview.html.twig #}

    {% extends '@App/layout.html.twig' %}

    {% use '@SonataAdmin/CRUD/base_edit_form.html.twig' with form as parentForm %}

    {% import '@SonataAdmin/CRUD/base_edit_form_macro.html.twig' as form_helper %}

    {# a block in '@App/layout.html.twig' expecting article #}
    {% block templateContent %}
        {% set article = object %}

        {{ parent() }}

        <div class="sonata-preview-form-container">
            {{ block('parentForm') }}
        </div>
    {% endblock %}

    {% block formactions %}
        <button class="btn btn-success" type="submit" name="btn_preview_approve">
            <i class="fas fa-check"></i>
            {{ 'btn_preview_approve'|trans({}, 'SonataAdminBundle') }}
        </button>
        <button class="btn btn-danger" type="submit" name="btn_preview_decline">
            <i class="fas fa-times"></i>
            {{ 'btn_preview_decline'|trans({}, 'SonataAdminBundle') }}
        </button>
    {% endblock %}

Keep in mind that the whole edit form will now appear in your view.
Hiding the fieldset tags with css ``display:none`` will be enough to only show the buttons
(which still have to be styled according to your wishes) and create a nice preview-workflow:

.. code-block:: css

    .sonata-preview-form-container .row {
        display: none;
    };

Or if you prefer less:

.. code-block:: scss

    div.sonata-preview-form-container {
        .row {
            display: none;
        };
    }
