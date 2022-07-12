import { __ } from '@wordpress/i18n';

import { 
	InspectorControls,
	useBlockProps 
} from '@wordpress/block-editor';

import { store as coreStore } from '@wordpress/core-data';
import {
	PanelBody,
	PanelRow,
	SelectControl,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';

import { useSelect, useState, useEffect } from '@wordpress/data';
import './editor.scss';

const {createElement} = wp.element;
const el = createElement;
export default function edit(props) {

	function removeHTML(str) {
		var div = document.createElement("div");
		div.innerHTML = str;
		return div.textContent || div.innerText || "";
	}
	const { attributes: { numberPosts, text, postType }, setAttributes } = props;

	const blockProps = useBlockProps(
		{ className: "has-" + postType }
	);
	
	// ? https://stackoverflow.com/questions/71370680/how-can-i-make-a-dropdown-list-control-that-fetches-names-of-custom-posts-types
    const postTypes = useSelect(
        (select) => select(coreStore).getPostTypes({ per_page: -1 }), []
    );

    // Options expects [{label: ..., value: ...}]
    var postTypeOptions = !Array.isArray(postTypes) ? postTypes : postTypes
        .filter(
            // Filter out internal WP post types eg: wp_block, wp_navigation, wp_template, wp_template_part..
            postType => postType.viewable == true)
        .map(
            // Format the options for display in the <SelectControl/>
            (postType) => ({
                label: postType.labels.singular_name,
                value: postType.slug, // the value saved as postType in attributes
            })
        );
		
	// !! POSTS
	const posts = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'postType', postType, {
			per_page: numberPosts,
			sticky: true,
		} );
	});
	if (posts != null && posts.length > 0) {
		// !! See design
		text.length = 0;
		var list = [];
		var block = [];
		var counter = 0;

		posts.forEach(post => {
			var block = [];
			counter++;
			var excerpt = '';
			var dateClass = '';
			if (post.excerpt != null) {
				excerpt = post.excerpt.rendered;
			} else if (post.content != null) {
				excerpt = post.content.rendered;
				excerpt = removeHTML(excerpt);
				excerpt = excerpt.slice(0,130);
			} else {
				excerpt = 'empty content';
			}
			if (post.title.rendered != null && excerpt != null && post .link != null) {
				var title = post.title.rendered ? post.title.rendered : 'Title';
				var innerBlock = [
					el('h2', {}, title),
					<p dangerouslySetInnerHTML={ { __html: excerpt } }></p>,
				];
				block.push( 
					el('div',{className:"content"}, innerBlock),
				);
			}
			list.push(el('div',{className: "block" + dateClass}, block));
		});

		text.push(list);
		// text.push(el('hr',{ style:{backgroundColor: "gray"}}));
	}
	return (
		<>
            <InspectorControls>
                <PanelBody>
					<PanelRow>
					<SelectControl
						label="Select a Post Type"
						value={postType}
						options={postTypeOptions}
						onChange={(value) => setAttributes({ postType: value })}
					/>
					</PanelRow>
					<PanelRow>
					<NumberControl
                        label="Number of Posts"
						value={ numberPosts }
						onChange={ ( value ) => setAttributes( { numberPosts: value } ) }
						isShiftStepEnabled={ true }
						shiftStep={ 1 }
					/>
					</PanelRow>
                </PanelBody>
            </InspectorControls>
		<div { ...blockProps }>
				{ ! posts && 'Loading...' }
				{ posts && posts.length === 0 && 'No Posts' }
				{ posts && posts.length > 0 && <div class={"list-block posts-" + counter }> {text} </div> }
				{/* { posts && posts.length > 0 && text } */}
			</div>
        </>
	);
}
