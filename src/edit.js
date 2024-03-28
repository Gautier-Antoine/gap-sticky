import { __ } from '@wordpress/i18n';

import { 
	InspectorControls,
	useBlockProps 
} from '@wordpress/block-editor';

import { store as coreStore } from '@wordpress/core-data';
import {
	Spinner,
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';

import {
	useSelect,
} from '@wordpress/data';
import './editor.scss';

	// Add spinner : https://developer.wordpress.org/news/2023/05/19/useentityrecords-an-easier-way-to-fetch-wordpress-data/
	// ! See support for (colors, etc) https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/
	// See proper reload/design

const {createElement} = wp.element;
const el = createElement;
export default function edit(props) {

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
	let params = {per_page: numberPosts};
	if (isSticky === true) {
		params = {...params, sticky: true}
	}
	const {hasResolved, posts} = useSelect( select => {
		  return {
			posts: select(coreStore).getEntityRecords('postType', postType, params),
			hasResolved: select(coreStore).hasFinishedResolution('getEntityRecords', ['postType', postType, params])
		  }
		},
		[postType, params]
	);
	if (hasResolved && posts != null && posts.length > 0) {
		text.length = 0;
		let list = [];
		var counter = 0;

		posts.forEach(post => {
			let block = [];
			counter++;
			let excerpt = __('Empty excerpt', 'gap-sticky');
			let dateClass = '';
			if (post.excerpt !== null) {
				excerpt = post.excerpt.rendered;
			}
			if (post.title.rendered != null && excerpt != null && post .link != null) {
				let title = post.title.rendered ? post.title.rendered : __('The title', 'gap-sticky');
				let innerBlock = [
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
	}
	function LoopPosts ({ hasResolved, posts }) {
		if ( !hasResolved ) {
			return <Spinner />;
		}
		if ( !posts?.length ) {
			return <p>{ __('No Posts', 'gap-sticky') }</p>;
		}
		return <div className={"list-block posts-" + counter }> {text} </div>;
	}
	return (
		<>
            <InspectorControls>
                <PanelBody>
				{/* https://developer.wordpress.org/block-editor/reference-guides/components/toggle-control/ */}
					<PanelRow>
						<ToggleControl
							label={__('Only Sticky posts?', 'gap-sticky')}
							help={isSticky ? __('Displays only sticky post', 'gap-sticky') : __('Displays all posts', 'gap-sticky')}
							checked={isSticky}
							onChange={(value) => setAttributes({ isSticky: value })}
						/>
					</PanelRow>
					<PanelRow>
						<SelectControl
							label={__('Select a Post Type', 'gap-sticky')}
							value={postType}
							options={postTypeOptions}
							onChange={(value) => setAttributes({ postType: value })}
						/>
					</PanelRow>
					<PanelRow>
						<NumberControl
							label={__('Number of Posts', 'gap-sticky')}
							value={numberPosts}
							onChange={(value) => setAttributes({numberPosts: parseInt(value, 10)})}
							isShiftStepEnabled={true}
							shiftStep={1}
						/>
					</PanelRow>
                </PanelBody>
            </InspectorControls>
			<div {...blockProps}>
				<LoopPosts hasResolved={ hasResolved } posts={ posts } />
			</div>
        </>
	);
}
