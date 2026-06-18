<?php
/**
 * Built-in SVG icon registry for ART Starter.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Icons
 */
class Art_Starter_Icons {

	const CATEGORY_SOCIAL  = 'social';
	const CATEGORY_ACTION  = 'action';
	const CATEGORY_GENERAL = 'general';

	const DEFAULT_LINK_ICON = 'link';

	/**
	 * Categories available in the admin icon picker (CTA, links, etc.).
	 *
	 * @return array<int, string>
	 */
	public static function get_picker_categories() {
		return array(
			self::CATEGORY_SOCIAL,
			self::CATEGORY_ACTION,
			self::CATEGORY_GENERAL,
		);
	}

	/**
	 * @return array<string, array{label: string, category: string, svg: string}>
	 */
	public static function get_registry() {
		static $registry = null;

		if ( null !== $registry ) {
			return $registry;
		}

		$registry = array(
			'telegram'      => array(
				'label'    => 'Telegram',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.8 4.2 2.7 11.1c-1.2.5-1.2 1.2-.2 1.5l4.9 1.5 1.9 5.8c.2.7.6.9 1.1.9.4 0 .6-.2.9-.7l2.7-2.6 4.8 3.5c.9.5 1.5.2 1.7-1.1L23.5 5.5c.3-1.3-.5-1.9-1.7-1.3Z"/></svg>',
			),
			'vk'            => array(
				'label'    => __( 'ВКонтакте', 'art-starter' ),
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.785 16.241s.336-.039.508-.233c.158-.172.154-.497.154-.753 0-.41.006-8.335.006-8.335s0-.223.056-.342c.056-.119.158-.204.28-.255.221-.097 1.185-1.097 1.185-1.097s.098-.073.098-.17 0-.119-.098-.17l-1.652-.006s-.374-.006-.547.113c-.113.079-.19.259-.19.259s-.34.904-.792 1.672c-.956 1.583-1.339 1.666-1.495 1.666-.113 0-.224-.079-.224-.602v-3.104c0-.511.015-.813-.224-.98-.171-.117-.491-.154-1.286-.164-.985-.015-1.821.006-2.292.196-.158.068-.28.22-.205.229.092.012.301.056.411.205.143.196.137.638.137.638s.083 2.451-.19 2.754c-.19.226-.563.237-.563.237H5.252s-.855-.012-1.012.393c-.073.196-.056 1.512-.056 1.512h2.667s.399-.006.564.226c.393.533.393 1.581.393 1.581s.025 2.335-.184 2.626c-.178.246-.508.207-.508.207H4.587s-1.215-.037-1.711-1.067L2.59 10.44s-.263-.56.184-.823c.363-.203.854-.135.854-.135l3.014-.019s.22-.037.38.073c.16.111.258.369.258.369s.491 1.243 1.148 2.363c.694 1.161 1.557 2.162 1.557 2.162s.135.111.307.073c.184-.037 0-1.056 0-2.066 0-1.111-.079-1.581-.282-1.8-.215-.233-.614-.307-.614-.307s.491-.037 1.262-.056c.971-.025 1.697.019 2.188.215.331.135.589.429.779.834.196.411.147 1.808.147 1.808s.086 2.521-.196 2.86c-.196.227-.564.171-.564.171h-2.03s-1.826.115-4.083-1.659c-1.48-1.237-3.21-5.041-3.21-5.041s-.301-.632.209-.97c.363-.227 1.619-1.056 1.619-1.056s.122-.073.196-.233c.062-.135.037-.331.037-.331V6.926s-.006-.749.564-.97c.429-.171 1.52-.331 3.345-.429 1.263-.073 2.621-.056 2.621-.056h.627s.467-.031.712.147c.171.135.258.429.258.429s.049 1.243-.098 2.066c-.122.737-.429 1.193-.429 1.193s-.037.171 0 .282c.092.288.429.374.429.374s1.544.515 3.295 2.004c1.006.883 1.773 1.974 1.773 1.974s.129.22.037.442c-.062.147-.282.196-.282.196l-2.056.013z"/></svg>',
			),
			'youtube'       => array(
				'label'    => 'YouTube',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18 5 12 5 12 5s-6 0-7.8.4a2.5 2.5 0 0 0-1.8 1.8C2 9 2 12 2 12s0 3 .4 4.8a2.5 2.5 0 0 0 1.8 1.8C6 19 12 19 12 19s6 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.8.4-4.8.4-4.8s0-3-.4-4.8ZM10 15.5V8.5l5.5 3.5L10 15.5Z"/></svg>',
			),
			'instagram'     => array(
				'label'    => 'Instagram',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="4"/><circle cx="12" cy="12" r="3.5"/><circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none"/></svg>',
			),
			'mail'          => array(
				'label'    => 'Email',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>',
			),
			'whatsapp'      => array(
				'label'    => 'WhatsApp',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>',
			),
			'facebook'      => array(
				'label'    => 'Facebook',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12.07C22 6.48 17.52 2 11.93 2S2 6.48 2 12.07c0 4.99 3.66 9.13 8.44 9.93v-6.99H7.9v-2.94h2.54V9.41c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.94h-2.34v6.99c4.78-.8 8.44-4.94 8.44-9.93z"/></svg>',
			),
			'zen'           => array(
				'label'    => __( 'Яндекс Дзен', 'art-starter' ),
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.48 2.04a.74.74 0 0 1 .74-.04l7.78 4.49a.74.74 0 0 1 .37.64v8.98a.74.74 0 0 1-.37.64l-7.78 4.49a.74.74 0 0 1-.74-.04.74.74 0 0 1-.33-.6V2.64a.74.74 0 0 1 .33-.6zm.37 2.17-6.42 3.7 6.42 3.7 6.42-3.7-6.42-3.7zm-1.11 5.18v7.4l5.68 3.27v-7.4L10.74 9.39z"/></svg>',
			),
			'max'           => array(
				'label'    => 'MAX',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 4h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-5.1l-3.55 3.55a1 1 0 0 1-1.7-.7V16H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm2.2 5.4h1.45l1.55 2.55 1.55-2.55H14v5.2h-1.55v-3.1l-1.7 2.8h-.95l-1.7-2.8v3.1H7.2V9.4zm8.1 0H18v5.2h-1.55V9.4z"/></svg>',
			),
			'tiktok'        => array(
				'label'    => 'TikTok',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.6 5.82s.51.5 0 0A4.28 4.28 0 0 1 15.54 3h-3.09v12.4a2.59 2.59 0 0 1-2.59 2.5c-1.42 0-2.6-1.16-2.6-2.6 0-1.72 1.66-3.01 3.37-2.48V9.66c-3.45-.46-6.47 2.22-6.47 5.64 0 3.33 2.76 5.7 5.69 5.7 3.14 0 5.69-2.55 5.69-5.7V9.01a7.35 7.35 0 0 0 4.3 1.38V7.3a4.1 4.1 0 0 1-1.04-.14z"/></svg>',
			),
			'x'             => array(
				'label'    => 'X (Twitter)',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.3 3h3.2l-7 8.01 8.2 9.99h-6.4l-5.01-6.55-5.73 6.55H1.35l7.48-8.55L1 3h6.57l4.53 5.99L17.3 3zm-1.12 16.2h1.77L7.03 4.74H5.14l11.04 14.46z"/></svg>',
			),
			'linkedin'      => array(
				'label'    => 'LinkedIn',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.22 8.25h4.56V23H.22V8.25zM8.09 8.25h4.37v2.01h.06c.61-1.16 2.1-2.38 4.32-2.38 4.62 0 5.47 3.04 5.47 6.99V23h-4.56v-7.1c0-1.69-.03-3.87-2.36-3.87-2.36 0-2.72 1.84-2.72 3.75V23H8.09V8.25z"/></svg>',
			),
			'viber'         => array(
				'label'    => 'Viber',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.4 0C9.43.03 4.99.45 2.66 3.52 1.22 5.4.96 8.29 1.2 11.79c.24 3.5.64 10.08 6.14 11.74h.01l-.01 2.3s-.05.64.4.77c.51.16 1.14-.33 2.03-1.07 1.04-.87 1.64-1.41 2.37-2.05 6.52.55 11.54-2.09 12.12-2.62 1.32-1.18 2.94-4.26 3.07-8.37.13-4.11-.53-7.2-3.53-9.5C19.13.55 14.44.03 11.4 0zm.08 1.92c2.77.03 7.01.5 9.24 2.38 2.55 2.14 3.11 4.84 3 8.35-.11 3.51-1.47 6.05-2.47 6.94-.47.42-4.94 2.72-10.76 2.25l-2.74 2.12-.03-2.36C4.1 20.55 1.7 14.8 1.5 11.7 1.3 8.6 1.52 6.08 2.7 4.5c1.97-2.6 6.01-2.61 8.78-2.58zm-.19 3.95c-.4 0-.72.32-.72.72v.36c-2.14.36-3.58 2.01-3.58 4.18 0 .4.32.72.72.72s.72-.32.72-.72c0-1.55 1.26-2.81 2.81-2.81.4 0 .72-.32.72-.72v-.36c0-.4-.32-.72-.72-.72zm3.96 0c-.4 0-.72.32-.72.72v.36c-1.55 0-2.81 1.26-2.81 2.81 0 .4.32.72.72.72s.72-.32.72-.72c0-.86.7-1.56 1.56-1.56.4 0 .72-.32.72-.72v-.36c0-.4-.32-.72-.72-.72zm3.96 0c-.4 0-.72.32-.72.72v.36c-.86 0-1.56.7-1.56 1.56 0 .4.32.72.72.72s.72-.32.72-.72c0 0 0 0 0 0 .86 0 1.56-.7 1.56-1.56.4 0 .72-.32.72-.72v-.36c0-.4-.32-.72-.72-.72z"/></svg>',
			),
			'discord'       => array(
				'label'    => 'Discord',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.93 4.26A16.28 16.28 0 0 0 14.54 3l-.2.36a15.1 15.1 0 0 1 3.64 1.78 12.2 12.2 0 0 0-9.76 0A14.9 14.9 0 0 1 11.82 3.3l-.2-.36a16.2 16.2 0 0 0-4.39 1.26C2.64 8.04 1.73 11.7 2.1 15.3a16.4 16.4 0 0 0 4.97 2.52l.39-.5a11 11 0 0 1-2.08-1.05l.35-.27c4.01 1.87 8.35 1.87 12.3 0l.35.27a10.8 10.8 0 0 1-2.09 1.06l.39.5a16.3 16.3 0 0 0 4.97-2.52c.5-4.2-.2-7.84-3.05-11.04zM8.68 13.18c-.97 0-1.77-.89-1.77-1.98 0-1.09.78-1.98 1.77-1.98s1.78.89 1.77 1.98c0 1.09-.78 1.98-1.77 1.98zm6.64 0c-.97 0-1.77-.89-1.77-1.98 0-1.09.78-1.98 1.77-1.98s1.78.89 1.77 1.98c0 1.09-.78 1.98-1.77 1.98z"/></svg>',
			),
			'rutube'        => array(
				'label'    => 'Rutube',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm3.2 6.2v5.6L16 12l-7.8-1.8z"/></svg>',
			),
			'ok'            => array(
				'label'    => __( 'Одноклассники', 'art-starter' ),
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 4.2c2.1 0 3.8 1.7 3.8 3.8S14.1 11.8 12 11.8 8.2 10.1 8.2 8 9.9 4.2 12 4.2zm0 10.3c3.1 0 5.9 1.6 7.5 4.1l-1.7 1.1a8.2 8.2 0 0 0-11.6 0L5.5 18.6c1.6-2.5 4.4-4.1 7.5-4.1zm-4.1 1.9c.8 0 1.4.6 1.4 1.4s-.6 1.4-1.4 1.4-1.4-.6-1.4-1.4.6-1.4 1.4-1.4zm8.2 0c.8 0 1.4.6 1.4 1.4s-.6 1.4-1.4 1.4-1.4-.6-1.4-1.4.6-1.4 1.4-1.4z"/></svg>',
			),
			'github'        => array(
				'label'    => 'GitHub',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.58 2 12.26c0 4.52 2.87 8.35 6.84 9.7.5.1.68-.22.68-.49 0-.24-.01-.87-.01-1.7-2.78.62-3.37-1.37-3.37-1.37-.45-1.17-1.11-1.48-1.11-1.48-.91-.64.07-.63.07-.63 1 .07 1.53 1.05 1.53 1.05.9 1.56 2.36 1.11 2.94.85.09-.67.35-1.11.63-1.37-2.22-.26-4.56-1.14-4.56-5.07 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.3.1-2.7 0 0 .84-.28 2.75 1.05A9.2 9.2 0 0 1 12 6.84c.85.004 1.71.12 2.51.35 1.91-1.33 2.75-1.05 2.75-1.05.55 1.4.2 2.44.1 2.7.64.72 1.03 1.63 1.03 2.75 0 3.94-2.34 4.81-4.57 5.07.36.32.68.94.68 1.9 0 1.37-.01 2.47-.01 2.8 0 .27.18.6.69.49A10.03 10.03 0 0 0 22 12.26C22 6.58 17.52 2 12 2z"/></svg>',
			),
			'spotify'       => array(
				'label'    => 'Spotify',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1.5C6.2 1.5 1.5 6.2 1.5 12S6.2 22.5 12 22.5 22.5 17.8 22.5 12 17.8 1.5 12 1.5zm5.3 15.9a.8.8 0 0 1-1.1.3c-3-1.9-6.8-2.3-11.3-1.2a.8.8 0 0 1-.4-1.5c4.9-1.2 9.1-.7 12.5 1.4.4.2.5.7.3 1zm1.4-3.2a1 1 0 0 1-1.3.3c-3.4-2.1-8.6-2.7-12.6-1.5a1 1 0 0 1-.6-1.9c4.6-1.4 10.4-.7 14.3 1.7.5.3.6.9.2 1.4zm.1-3.4C14.1 8.2 8.2 8 4.6 9.2a1.2 1.2 0 0 1-1.4-.9 1.2 1.2 0 0 1 .9-1.4c4.1-1.3 10.7-1.1 14.9 1.4a1.2 1.2 0 0 1 .4 1.6 1.2 1.2 0 0 1-1.6.4z"/></svg>',
			),
			'twitch'        => array(
				'label'    => 'Twitch',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 3 2 7.5v12h5v3l3-3h4.5L21 13.5V3H4zm16 9.5-3 3h-4.5l-2.2 2.2V15H7V5h13v7.5zM14 7h2v5h-2V7zm-4 0h2v5h-2V7z"/></svg>',
			),
			'pinterest'     => array(
				'label'    => 'Pinterest',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a9.7 9.7 0 0 0-3.4 18.8c-.04-.8-.08-2 .17-3 .18-.77 1.2-5.2 1.2-5.2s-.3-.6-.3-1.5c0-1.4.8-2.5 1.9-2.5.9 0 1.3.7 1.3 1.5 0 .9-.6 2.3-.9 3.6-.26 1.1.55 2 1.6 2 2 0 3.4-2.1 3.4-5.1 0-2.7-1.9-4.6-4.7-4.6a5.1 5.1 0 0 0-5.3 5.2c0 1 .4 1.7.9 2.2a.4.4 0 0 1 .1.4c-.01.15-.04.48-.06.6-.02.2-.1.24-.23.15-1.3-.6-2.1-2.5-2.1-4.5 0-3.7 2.7-7.1 7.8-7.1 4.1 0 7.3 2.9 7.3 6.8 0 4.1-2.6 7.4-6.2 7.4-1.2 0-2.4-.6-2.8-1.4l-.8 3c-.3.9-1 2.1-1.5 2.8A9.7 9.7 0 1 0 12 2z"/></svg>',
			),
			'snapchat'      => array(
				'label'    => 'Snapchat',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2c2.8 0 3.1.02 4.2.08 1.1.06 1.7.27 2.1.45.53.22.9.48 1.3.92.4.44.66.8.88 1.33.18.4.39 1 .45 2.1.06 1.1.08 1.4.08 4.2s-.02 3.1-.08 4.2c-.06 1.1-.27 1.7-.45 2.1a3.5 3.5 0 0 1-.88 1.33 3.5 3.5 0 0 1-1.3.92c-.4.18-1 .39-2.1.45-1.1.06-1.4.08-4.2.08s-3.1-.02-4.2-.08c-1.1-.06-1.7-.27-2.1-.45a3.5 3.5 0 0 1-1.3-.92 3.5 3.5 0 0 1-.88-1.33c-.18-.4-.39-1-.45-2.1C2.02 15.1 2 14.8 2 12s.02-3.1.08-4.2c.06-1.1.27-1.7.45-2.1.22-.53.48-.9.92-1.3.44-.4.8-.66 1.33-.88.4-.18 1-.39 2.1-.45C8.9 2.02 9.2 2 12 2zm0 1.8c-2.7 0-3 .02-4.1.07-.9.05-1.4.23-1.7.36-.43.18-.74.4-1.07.73-.33.33-.55.64-.73 1.07-.13.3-.31.8-.36 1.7-.05 1-.07 1.3-.07 4.1s.02 3 .07 4.1c.05.9.23 1.4.36 1.7.18.43.4.74.73 1.07.33.33.64.55 1.07.73.3.13.8.31 1.7.36 1 .05 1.3.07 4.1.07s3-.02 4.1-.07c.9-.05 1.4-.23 1.7-.36.43-.18.74-.4 1.07-.73.33-.33.55-.64.73-1.07.13-.3.31-.8.36-1.7.05-1 .07-1.3.07-4.1s-.02-3-.07-4.1c-.05-.9-.23-1.4-.36-1.7a2.9 2.9 0 0 0-.73-1.07 2.9 2.9 0 0 0-1.07-.73c-.3-.13-.8-.31-1.7-.36-1-.05-1.3-.07-4.1-.07z"/></svg>',
			),
			'threads'       => array(
				'label'    => 'Threads',
				'category' => self::CATEGORY_SOCIAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.5 0 6.2 1.1 8 3.3-.7-.3-1.5-.5-2.3-.5-2 0-3.6 1.6-3.6 3.6 0 .3 0 .6.1.9-1.6-.2-3.1-.1-4.5.4-2.2.8-3.5 2.4-3.5 4.4 0 2.7 2.5 4.8 6.5 4.8 4.8 0 8.3-2.2 8.3-6.7 0-4.8-3.5-8-8.8-8-4.8 0-8.1 2.8-8.1 6.5 0 2.1 1.1 3.5 2.8 3.5.9 0 1.6-.5 1.9-1.3h.1c.2.8.8 1.3 1.7 1.3 1.1 0 1.9-.9 1.9-2.2 0-2.5-2-4.2-5.1-4.2-3.4 0-5.8 2.1-5.8 5.2 0 3.2 2.5 5.4 6.2 5.4 2.2 0 4.1-.7 5.4-2-.9 3.6-4 5.9-8.4 5.9-5.2 0-8.6-3.2-8.6-8.1C3.4 6.4 7.1 2.2 12 2.2z"/></svg>',
			),
			'calendar'      => array(
				'label'    => __( 'Календарь', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>',
			),
			'message'       => array(
				'label'    => __( 'Сообщение', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5h16v11H7l-3 3V5z"/></svg>',
			),
			'phone'         => array(
				'label'    => __( 'Телефон', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 3h3l1.5 5-2 1.2a13 13 0 0 0 5.3 5.3L15 12.5 20 14v3a2 2 0 0 1-2.2 2A16 16 0 0 1 5 7.2 2 2 0 0 1 7 3z"/></svg>',
			),
			'arrow-right'   => array(
				'label'    => __( 'Стрелка', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
			),
			'cart'          => array(
				'label'    => __( 'Корзина', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/><path d="M3 4h2l2.2 9.4a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H7"/></svg>',
			),
			'star'          => array(
				'label'    => __( 'Звезда', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 3.5 2.7 5.5 6 .9-4.3 4.2 1 6-5.4-2.8-5.4 2.8 1-6L3.3 9.9l6-.9L12 3.5z"/></svg>',
			),
			'download'      => array(
				'label'    => __( 'Скачать', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 4v10M8 10l4 4 4-4"/><path d="M5 18h14"/></svg>',
			),
			'bookmark'      => array(
				'label'    => __( 'Закладка', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 4h10v16l-5-3-5 3V4z"/></svg>',
			),
			'bell'          => array(
				'label'    => __( 'Уведомление', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M18 16H6l1.5-1.5V11a5.5 5.5 0 0 1 11 0v3.5L18 16z"/><path d="M10 18a2 2 0 0 0 4 0"/></svg>',
			),
			'map-pin'       => array(
				'label'    => __( 'Адрес', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s6-5.2 6-10a6 6 0 1 0-12 0c0 4.8 6 10 6 10z"/><circle cx="12" cy="11" r="2.5"/></svg>',
			),
			'clock'         => array(
				'label'    => __( 'Время', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M12 8v5l3 2"/></svg>',
			),
			'play'          => array(
				'label'    => __( 'Воспроизвести', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7L8 5z"/></svg>',
			),
			'send'          => array(
				'label'    => __( 'Отправить', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 12 16-7-4 7 4 7-16-7z"/></svg>',
			),
			'gift'          => array(
				'label'    => __( 'Подарок', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="1"/><path d="M12 10V20M4 10h16M8.5 10C7 10 6 8.8 6 7.5S7 5 8.5 5 11 6.2 11 7.5M12 10c0-1.3 1-2.5 2.5-2.5S17 6.2 17 7.5 16 10 14.5 10"/></svg>',
			),
			'user'          => array(
				'label'    => __( 'Профиль', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c1.5-3 4-4.5 7-4.5s5.5 1.5 7 4.5"/></svg>',
			),
			'users'         => array(
				'label'    => __( 'Группа', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 20c1-2.5 3-4 6-4"/><circle cx="17" cy="9" r="2.5"/><path d="M14 20c.5-1.8 2-3 4-3"/></svg>',
			),
			'mic'           => array(
				'label'    => __( 'Микрофон', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="9" y="4" width="6" height="10" rx="3"/><path d="M6 11a6 6 0 0 0 12 0M12 17v3"/></svg>',
			),
			'camera'        => array(
				'label'    => __( 'Камера', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 8h4l2-2h4l2 2h4v10H4V8z"/><circle cx="12" cy="13" r="3"/></svg>',
			),
			'plus-circle'   => array(
				'label'    => __( 'Добавить', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M12 9v6M9 12h6"/></svg>',
			),
			'check-circle'  => array(
				'label'    => __( 'Готово', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="m8.5 12.2 2.2 2.2 4.8-4.8"/></svg>',
			),
			'zap'           => array(
				'label'    => __( 'Молния', 'art-starter' ),
				'category' => self::CATEGORY_ACTION,
				'svg'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/></svg>',
			),
			'link'          => array(
				'label'    => __( 'Ссылка', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 14a3.5 3.5 0 0 0 5 0l2-2a3.5 3.5 0 0 0-5-5l-1 1"/><path d="M14 10a3.5 3.5 0 0 0-5 0l-2 2a3.5 3.5 0 0 0 5 5l1-1"/></svg>',
			),
			'video'         => array(
				'label'    => __( 'Видео', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="6" width="13" height="12" rx="2"/><path d="m16 10 5-3v10l-5-3z"/></svg>',
			),
			'shop'          => array(
				'label'    => __( 'Магазин', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 9h16l-1.2 10H5.2L4 9z"/><path d="M8 9V7a4 4 0 0 1 8 0v2"/></svg>',
			),
			'file'          => array(
				'label'    => __( 'Файл', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 4h7l5 5v11a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1z"/><path d="M15 4v5h5"/></svg>',
			),
			'external-link' => array(
				'label'    => __( 'Внешняя ссылка', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M14 4h6v6"/><path d="M10 14 20 4"/><path d="M20 14v6H4V4h6"/></svg>',
			),
			'heart'         => array(
				'label'    => __( 'Сердце', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 20s-7-4.4-7-9.2a4 4 0 0 1 7-2.4 4 4 0 0 1 7 2.4C19 15.6 12 20 12 20z"/></svg>',
			),
			'home'          => array(
				'label'    => __( 'Дом', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5z"/></svg>',
			),
			'globe'         => array(
				'label'    => __( 'Сайт', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M4 12h16M12 4a12 12 0 0 1 0 16M12 4a12 12 0 0 0 0 16"/></svg>',
			),
			'info'          => array(
				'label'    => __( 'Информация', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M12 11v5M12 8h.01"/></svg>',
			),
			'book-open'     => array(
				'label'    => __( 'Книга', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H12v16H6.5A2.5 2.5 0 0 1 4 17.5V6.5z"/><path d="M12 4h5.5A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5H12"/></svg>',
			),
			'briefcase'     => array(
				'label'    => __( 'Работа', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="8" width="16" height="11" rx="1"/><path d="M9 8V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>',
			),
			'music'         => array(
				'label'    => __( 'Музыка', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M11 6v10.5a2.5 2.5 0 1 1-2-2.4V8l8-2v8.5a2.5 2.5 0 1 1-2-2.4"/></svg>',
			),
			'image'         => array(
				'label'    => __( 'Изображение', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="6" width="16" height="12" rx="2"/><circle cx="9" cy="11" r="1.5"/><path d="m6 16 4-4 3 3 2-2 3 3"/></svg>',
			),
			'tag'           => array(
				'label'    => __( 'Тег', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 12V5a1 1 0 0 1 1-1h7l8 8-7 7-8-8z"/><circle cx="9" cy="9" r="1"/></svg>',
			),
			'award'         => array(
				'label'    => __( 'Награда', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="9" r="4"/><path d="M8.5 14 7 20l5-2 5 2-1.5-6"/></svg>',
			),
			'pen'           => array(
				'label'    => __( 'Редактировать', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 20h4l10-10-4-4L4 16v4z"/></svg>',
			),
			'coffee'        => array(
				'label'    => __( 'Кофе', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 8h10v5a4 4 0 0 1-4 4H8a2 2 0 0 1-2-2V8z"/><path d="M16 10h2a2 2 0 0 1 0 4h-2M7 4v2M11 4v2M15 4v2"/></svg>',
			),
			'graduation-cap' => array(
				'label'    => __( 'Обучение', 'art-starter' ),
				'category' => self::CATEGORY_GENERAL,
				'svg'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 9.5 12 5l9 4.5-9 4.5-9-4.5z"/><path d="M7 12.5V16a5 5 0 0 0 10 0v-3.5"/></svg>',
			),
		);

		$file_icons = array(
			'vk'  => 'vk.svg',
			'ok'  => 'ok.svg',
			'zen' => 'dzen.svg',
			'max' => 'max.svg',
		);

		foreach ( $file_icons as $slug => $filename ) {
			if ( ! isset( $registry[ $slug ] ) ) {
				continue;
			}

			$svg = self::load_social_svg( $filename );
			if ( '' !== $svg ) {
				$registry[ $slug ]['svg'] = $svg;
			}
		}

		return $registry;
	}

	/**
	 * Load SVG icon from assets/icons/social/.
	 *
	 * @param string $filename SVG filename.
	 * @return string
	 */
	private static function load_social_svg( $filename ) {
		$filename = basename( (string) $filename );
		if ( ! preg_match( '/\.svg$/i', $filename ) ) {
			return '';
		}

		$path = ART_STARTER_PLUGIN_DIR . 'assets/icons/social/' . $filename;
		if ( ! is_readable( $path ) ) {
			return '';
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local plugin asset.
		$svg = (string) file_get_contents( $path );
		if ( '' === trim( $svg ) ) {
			return '';
		}

		$svg = preg_replace( '/<\?xml.*?\?>\s*/is', '', $svg );
		$svg = preg_replace( '/<!DOCTYPE.*?>\s*/is', '', $svg );
		$svg = preg_replace( '/<!--.*?-->\s*/is', '', $svg );
		$svg = preg_replace( '/<title>.*?<\/title>\s*/is', '', $svg );
		$svg = preg_replace( '/\s(width|height)="[^"]*"/i', '', $svg );

		if ( ! preg_match( '/aria-hidden/i', $svg ) ) {
			$svg = preg_replace( '/<svg/i', '<svg aria-hidden="true"', $svg, 1 );
		}

		return trim( (string) $svg );
	}

	/**
	 * @return array<string, string>
	 */
	public static function get_category_labels() {
		return array(
			self::CATEGORY_SOCIAL  => __( 'Соцсети', 'art-starter' ),
			self::CATEGORY_ACTION  => __( 'Действия', 'art-starter' ),
			self::CATEGORY_GENERAL => __( 'Общие', 'art-starter' ),
		);
	}

	/**
	 * @return array<string, string>
	 */
	public static function get_social_networks() {
		$networks = array();

		foreach ( self::get_registry() as $slug => $icon ) {
			if ( self::CATEGORY_SOCIAL !== $icon['category'] ) {
				continue;
			}

			$networks[ $slug ] = (string) $icon['label'];
		}

		return $networks;
	}

	/**
	 * @param string $slug Icon slug.
	 * @return array{label: string, category: string, svg: string}|null
	 */
	public static function get( $slug ) {
		$slug     = sanitize_key( (string) $slug );
		$registry = self::get_registry();

		return isset( $registry[ $slug ] ) ? $registry[ $slug ] : null;
	}

	/**
	 * @param string               $slug       Icon slug.
	 * @param array<int, string>   $categories Allowed categories.
	 * @param bool                 $allow_empty Allow empty slug.
	 * @return string
	 */
	public static function sanitize_slug( $slug, $categories = array(), $allow_empty = true ) {
		$slug = sanitize_key( (string) $slug );

		if ( '' === $slug ) {
			return $allow_empty ? '' : '';
		}

		$icon = self::get( $slug );
		if ( ! $icon ) {
			return '';
		}

		if ( ! empty( $categories ) && ! in_array( $icon['category'], $categories, true ) ) {
			return '';
		}

		return $slug;
	}

	/**
	 * @param string $slug Icon slug.
	 * @return string
	 */
	public static function get_effective_slug( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' !== $slug && self::get( $slug ) ) {
			return $slug;
		}

		return '';
	}

	/**
	 * Resolve icon slug for homepage link buttons.
	 *
	 * @param string $slug Stored icon slug.
	 * @return string
	 */
	public static function resolve_link_icon( $slug ) {
		$resolved = self::sanitize_slug( (string) $slug, self::get_picker_categories() );

		if ( '' !== $resolved ) {
			return $resolved;
		}

		return self::DEFAULT_LINK_ICON;
	}

	/**
	 * Render link button icon with default fallback.
	 *
	 * @param string $slug          Stored icon slug.
	 * @param string $wrapper_class Wrapper class.
	 * @return string
	 */
	public static function render_link_icon( $slug, $wrapper_class = 'art-starter-homepage-link__icon-svg' ) {
		return self::render(
			self::resolve_link_icon( $slug ),
			array( 'class' => $wrapper_class )
		);
	}

	/**
	 * Render icon markup or fallback letter.
	 *
	 * @param string $slug          Icon slug.
	 * @param string $fallback_text Letter fallback source.
	 * @param string $wrapper_class Wrapper class.
	 * @return string
	 */
	public static function render_or_letter( $slug, $fallback_text = '', $wrapper_class = 'art-starter-icon' ) {
		$icon = self::get( self::get_effective_slug( $slug ) );
		if ( $icon ) {
			return self::render( $slug, array( 'class' => $wrapper_class ) );
		}

		$letter = substr( trim( (string) $fallback_text ), 0, 1 );
		if ( '' === $letter ) {
			$letter = '•';
		}

		return '<span class="' . esc_attr( trim( $wrapper_class . ' art-starter-icon--letter' ) ) . '">' . esc_html( $letter ) . '</span>';
	}

	/**
	 * Render icon markup.
	 *
	 * @param string               $slug Icon slug.
	 * @param array<string, mixed> $args Render args.
	 * @return string
	 */
	public static function render( $slug, $args = array() ) {
		$icon = self::get( sanitize_key( (string) $slug ) );
		if ( ! $icon ) {
			return '';
		}

		$class = isset( $args['class'] ) ? (string) $args['class'] : 'art-starter-icon';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG from internal icon registry.
		return '<span class="' . esc_attr( $class ) . '" aria-hidden="true">' . $icon['svg'] . '</span>';
	}

	/**
	 * @return array<string, array{label: string, category: string, svg: string}>
	 */
	public static function get_for_js() {
		$icons = array();

		foreach ( self::get_registry() as $slug => $icon ) {
			$icons[ $slug ] = array(
				'label'    => $icon['label'],
				'category' => $icon['category'],
				'svg'      => $icon['svg'],
			);
		}

		return $icons;
	}
}
