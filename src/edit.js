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
	ToggleControl,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';

import {
	useSelect,
	// useState,
	// useEffect
} from '@wordpress/data';
import './editor.scss';

const {createElement} = wp.element;
const el = createElement;
export default function edit(props) {

	// function removeHTML(str) {
	// 	let div = document.createElement("div");
	// 	div.innerHTML = str;
	// 	return div.textContent || div.innerText || "";
	// }
	const {attributes: {numberPosts, text, postType, isSticky}, setAttributes} = props;

	const blockProps = useBlockProps({className: "has-" + postType});
	
	// ? https://stackoverflow.com/questions/71370680/how-can-i-make-a-dropdown-list-control-that-fetches-names-of-custom-posts-types
    const postTypes = useSelect(
        (select) => select(coreStore).getPostTypes({ per_page: -1 }), []
    );
    let postTypeOptions = !Array.isArray(postTypes) ? postTypes : postTypes
        .filter(postType => postType.viewable == true)
        .map((postType) => ({
                label: postType.labels.singular_name,
                value: postType.slug,
		}));
		
	// !! POSTS > Make reload is isSticky or numberPosts
	let params = {per_page: numberPosts};
	if (isSticky === true) {
		params = {...params, sticky: true}
	}
	const posts = useSelect((select) => {
		return select('core').getEntityRecords('postType', postType, params);
	});
	if (posts != null && posts.length > 0) {
		// !! See design
		text.length = 0;
		let list = [];
		// let block = [];
		var counter = 0;

		posts.forEach(post => {
			let block = [];
			counter++;
			let excerpt = 'empty excerpt';
			let dateClass = '';
			// if (post.content != null) {
			// 	excerpt = post.content.rendered;
			// 	excerpt = removeHTML(excerpt);
			// 	excerpt = excerpt.slice(0,130);
			// }
			if (post.excerpt !== null) {
				excerpt = post.excerpt.rendered;
			}
			if (post.title.rendered != null && excerpt != null && post .link != null) {
				let title = post.title.rendered ? post.title.rendered : 'Title';
				let innerBlock = [
					// el('h2', {}, title),
					<h2 dangerouslySetInnerHTML={ { __html: title } }></h2>,
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
				{/* https://developer.wordpress.org/block-editor/reference-guides/components/toggle-control/ */}
					<PanelRow>
						<ToggleControl
							label="Only Sticky posts?"
							help={isSticky ? 'Displays only sticky post' : 'Displays all posts'}
							checked={isSticky}
							onChange={(value) => setAttributes({ isSticky: value })}
						/>
					</PanelRow>
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
							value={numberPosts}
							onChange={(value) => setAttributes({numberPosts: parseInt(value, 10)})}
							isShiftStepEnabled={true}
							shiftStep={1}
						/>
					</PanelRow>
                </PanelBody>
            </InspectorControls>
		<div {...blockProps}>
				{! posts && 'Loading...'}
				{posts && posts.length === 0 && 'No Posts'}
				{posts && posts.length > 0 && <div class={"list-block posts-" + counter }> {text} </div>}
				{/* { posts && posts.length > 0 && text } */}
			</div>
        </>
	);
}
