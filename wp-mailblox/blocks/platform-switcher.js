(function ($, wp) {

    const { dispatch, select } = wp.data;

    // Block attributes that can contain merge tags
    const MERGE_TAG_ATTRS = {
        'email-builder/text':      ['content'],
        'email-builder/header':    ['content'],
        'email-builder/subheader': ['content'],
        'email-builder/button':    ['text'],
        'email-builder/footer':    ['footerText', 'address'],
    };

    // Return flat key→tag map for a platform slug, or null if unknown
    function getTagMap(slug) {
        var all = window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.all_platforms;
        return (all && all[slug]) ? all[slug] : null;
    }

    // Resolve empty string (inherit from preset) to the actual resolved platform slug
    function resolveSlug(slug) {
        if (slug) return slug;
        return (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.current_platform) || 'mailchimp';
    }

    // Recursively count replaceable tags across all blocks (dry run)
    function countReplacements(blocks, oldMap, newMap) {
        var count = 0;
        blocks.forEach(function (block) {
            var attrs = MERGE_TAG_ATTRS[block.name];
            if (attrs) {
                attrs.forEach(function (attr) {
                    var val = block.attributes[attr];
                    if (!val) return;
                    Object.keys(oldMap).forEach(function (key) {
                        if (newMap[key] && val.indexOf(oldMap[key]) !== -1) {
                            count++;
                        }
                    });
                });
            }
            if (block.innerBlocks && block.innerBlocks.length) {
                count += countReplacements(block.innerBlocks, oldMap, newMap);
            }
        });
        return count;
    }

    // Recursively collect tag strings from oldMap that appear in content but have no key in newMap
    function collectUnmappable(blocks, oldMap, newMap) {
        var tags = {};
        blocks.forEach(function (block) {
            var attrs = MERGE_TAG_ATTRS[block.name];
            if (attrs) {
                attrs.forEach(function (attr) {
                    var val = block.attributes[attr];
                    if (!val) return;
                    Object.keys(oldMap).forEach(function (key) {
                        if (!newMap[key] && val.indexOf(oldMap[key]) !== -1) {
                            tags[oldMap[key]] = true;
                        }
                    });
                });
            }
            if (block.innerBlocks && block.innerBlocks.length) {
                var inner = collectUnmappable(block.innerBlocks, oldMap, newMap);
                Object.keys(inner).forEach(function (k) { tags[k] = true; });
            }
        });
        return tags;
    }

    // Recursively walk all blocks and apply string replacements
    function applyReplacements(blocks, oldMap, newMap) {
        blocks.forEach(function (block) {
            var attrs = MERGE_TAG_ATTRS[block.name];
            if (attrs) {
                var updates = {};
                attrs.forEach(function (attr) {
                    var val = block.attributes[attr];
                    if (!val) return;
                    var updated = val;
                    Object.keys(oldMap).forEach(function (key) {
                        if (newMap[key]) {
                            updated = updated.split(oldMap[key]).join(newMap[key]);
                        }
                    });
                    if (updated !== val) {
                        updates[attr] = updated;
                    }
                });
                if (Object.keys(updates).length) {
                    dispatch('core/block-editor').updateBlockAttributes(block.clientId, updates);
                }
            }
            if (block.innerBlocks && block.innerBlocks.length) {
                applyReplacements(block.innerBlocks, oldMap, newMap);
            }
        });
    }

    // -------------------------------------------------------------------------
    // Reload after save when preset or platform changes
    // EB_EDITOR_DATA is PHP-generated on page load, so changing the preset or
    // platform override requires a reload to pick up the new fonts, tag maps, etc.
    // -------------------------------------------------------------------------

    var needsReload = false;
    var wasSaving   = false;

    $(document).on('change', '#eb_preset, #eb_platform_override', function () {
        needsReload = true;
    });

    wp.data.subscribe(function () {
        var editor   = wp.data.select('core/editor');
        var saving   = editor.isSavingPost() && !editor.isAutosavingPost();

        if (wasSaving && !saving) {
            if (needsReload && !editor.didPostSaveRequestFail()) {
                window.location.reload();
            }
        }

        wasSaving = saving;
    });

    $(document).on('change', '#eb_platform_override', function () {
        var $select  = $(this);
        var newSlug  = resolveSlug($select.val());
        var oldSlug  = resolveSlug(''); // current_platform is always the resolved slug

        if (newSlug === oldSlug) {
            if (window.EB_EDITOR_DATA) window.EB_EDITOR_DATA.current_platform = newSlug;
            return;
        }

        var oldMap = getTagMap(oldSlug);
        var newMap = getTagMap(newSlug);

        if (!oldMap || !newMap) {
            if (window.EB_EDITOR_DATA) window.EB_EDITOR_DATA.current_platform = newSlug;
            return;
        }

        var blocks         = select('core/block-editor').getBlocks();
        var found          = countReplacements(blocks, oldMap, newMap);
        var unmappable     = collectUnmappable(blocks, oldMap, newMap);
        var unmappableList = Object.keys(unmappable);

        if (found === 0 && unmappableList.length === 0) {
            if (window.EB_EDITOR_DATA) window.EB_EDITOR_DATA.current_platform = newSlug;
            return;
        }

        var newLabel = newSlug.charAt(0).toUpperCase() + newSlug.slice(1).replace(/_/g, ' ');
        var msg;

        if (found > 0 && unmappableList.length > 0) {
            msg = found + ' merge tag' + (found !== 1 ? 's' : '') + ' will be updated for ' + newLabel + '.\n\n'
                + unmappableList.length + ' tag' + (unmappableList.length !== 1 ? 's' : '') + ' ('
                + unmappableList.join(', ') + ') have no equivalent in ' + newLabel
                + ' and will remain in your content — you may want to remove them manually.\n\nContinue?';
        } else if (found > 0) {
            msg = found + ' merge tag' + (found !== 1 ? 's' : '') + ' will be updated for the new platform. Continue?';
        } else {
            msg = unmappableList.length + ' merge tag' + (unmappableList.length !== 1 ? 's' : '') + ' ('
                + unmappableList.join(', ') + ') have no equivalent in ' + newLabel
                + ' and will remain in your content. Continue?';
        }

        if (!window.confirm(msg)) {
            // Revert the select back to its previous value
            var prevVal = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.current_platform) || '';
            $select.val(prevVal === oldSlug ? '' : oldSlug);
            return;
        }

        if (found > 0) {
            applyReplacements(blocks, oldMap, newMap);
        }

        if (window.EB_EDITOR_DATA) window.EB_EDITOR_DATA.current_platform = newSlug;

        if (unmappableList.length > 0) {
            wp.data.dispatch('core/notices').createWarningNotice(
                'Platform switched to ' + newLabel + '. The following merge tags have no equivalent and were left in your content: '
                    + unmappableList.join(', ') + '. You may want to remove or replace them manually.',
                { id: 'eb-unmappable-tags', isDismissible: true }
            );
        }
    });

})(jQuery, window.wp);
